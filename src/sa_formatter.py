# sa_formatter.py

"""
Provides a method to filter and pretty a dataframe into a format more usable
by the correlator code.
"""

import string
import re
from pyspark.sql.functions import udf
from pyspark.sql.types import ArrayType, StringType

def remove_punctuation(text):
    # would use the built-in string.punctuation, but that removes +'s and #'s
    # which interfere with C++ and C#
    punctuation = "[]\"',-!?*^&%@;:()/"
    return text.translate(str.maketrans(punctuation, " " * len(punctuation)))

def remove_html_tags(text):
    tag_pattern = re.compile(r"</?[^<>]+>")
    return re.sub(tag_pattern, " ", text)

def remove_newlines(text):
    nl_pattern = re.compile(r"\n")
    return re.sub(nl_pattern, " ", text)

def to_lowercase(text):
    return text.lower()

def split_on_spaces(text):
    return text.split()

def only_unique(words):
    return set(words)

def only_from_set(words, keywords):
    return words & keywords

class SAFormatter:
    def total_format(self, text):
        text = remove_html_tags(text)
        text = remove_newlines(text)
        text = remove_punctuation(text)
        text = to_lowercase(text) # so that e.g. "Python" and "python" count as the same word
        words = split_on_spaces(text)
        words = only_unique(words)
        words = only_from_set(words, self._keyword_set)

        return list(words)

    def frame_to_words_frame(self, frame):
        words_frame = frame.withColumn("words", udf(self.total_format, ArrayType(StringType()))("description"))
        return words_frame

    def set_possible_keywords(self, kwset):
        self._keyword_set = kwset

