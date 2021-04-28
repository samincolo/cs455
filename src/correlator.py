"""
Does The Analysis :tm:
"""

import operator
import functools
from pyspark.ml.fpm import FPGrowth
from pyspark.sql.types import StructType, StructField, StringType

# Model parameters
MIN_SUPPORT = 0.01
MIN_CONFIDENCE = 0.01

def row_to_correlation_pair(row):
    base = functools.reduce(lambda a, b: a + '&' + b, sorted(row.antecedent))
    against = row.consequent[0]
    score = row.confidence
    return (base, [(against, score)])

def rdd_to_frame(rdd, keywords, spark):
    stype = list(map(lambda kw: StructField(kw, StringType(), True), keywords))
    stype.insert(0, StructField('keyword', StringType(), True))
    schema = StructType(stype)

    return spark.createDataFrame(rdd, schema = schema)

def merge_sorted(list1, list2):
    return sorted(list1 + list2, key = lambda pair: pair[0])

# Not all keywords in the keyword set exist for the set of jobs that contained
# another keyword.
# This function pads a row of correlations with a 0.0 for every keyword that 
# did not appear with it.
def fill_missing_row(row, keywords):
    corrs = row[1][:]
    i = 0
    while i < len(keywords):
        if i >= len(corrs) or corrs[i][0] != keywords[i]: 
            corrs.insert(i, (keywords[i], 0.0))
        i += 1
    return (row[0], corrs)
            

def fill_missing(keywords):
    return lambda row: fill_missing_row(row, keywords)

def flatten_tuple(row):
    return (row[0], *list(map(lambda pair: pair[1], row[1])))

"""
Takes in:
    frame: a dataframe containing information about a subset of the jobs in a
    particular city.
    keywords: the set of keywords to create correlations for.
    city: The city, in form "name, state code" that this RDD was filtered on.
    Rows in the RDD will only contain jobs in this city.
    spark: the spark session
Returns a dataframe full of correlations.
"""
def get_correlation(frame, keywords, city, spark):
    keywords = sorted(list(keywords))
    model = FPGrowth(itemsCol = "words", minSupport = MIN_SUPPORT, minConfidence = MIN_CONFIDENCE).fit(frame)

    kwrules = model.associationRules.rdd

    # literally MapReduce
    correlations = kwrules.map(row_to_correlation_pair)
    correlations = correlations.reduceByKey(merge_sorted)
    correlations = correlations.map(fill_missing(keywords))
    correlations = correlations.map(flatten_tuple)

    return rdd_to_frame(correlations, keywords, spark)

