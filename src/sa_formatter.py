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
    return text.translate(str.maketrans(string.punctuation, " " * len(string.punctuation)))

def remove_html_tags(text):
    tag_pattern = re.compile(r"</?[^<>]+>")
    return re.sub(tag_pattern, " ", text)

def remove_newlines(text):
    nl_pattern = re.compile(r"\n")
    return re.sub(nl_pattern, " ", text)

def split_on_spaces(text):
    return text.split()

def to_lowercase(text):
    return text.lower()

def frame_to_words_frame(frame):
    words = frame.withColumn("description", udf(remove_html_tags)("description"))
    words = words.withColumn("description", udf(remove_newlines)("description"))
    words = words.withColumn("description", udf(remove_punctuation)("description"))
    words = words.withColumn("description", udf(to_lowercase)("description"))
    words = words.withColumn("words", udf(split_on_spaces, ArrayType(StringType()))("description"))

    return words
