"""
Does The Analysis :tm:
"""

"""
Takes in:
    rdd: an RDD of pyspark.sql.Row containing information about a subset of the
    jobs in a particular city
    keyword: The keyword this RDD was filtered on. Rows in the RDD will only
    contain jobs with this keyword.
    city: The city, in form "name, state code" that this RDD was filtered on.
    Rows in the RDD will only contain jobs in this city.

Should output an RDD of pyspark.sql.Row for each word in the jobs that you find
to be interestingly correlated with the given keyword.

CITY: The city as passed into this functon
KEYWORD: The original keyword as passed into this function
CORRELATED_WORD: A word that was found to be correlated to the keyword in these jobs
CORRELATION_AMOUNT: Some measure of how highly correlated the KEYWORD and the CORRELATED_WORD are

For instance, on a particular invocation of this function, you may get 
keyword = "java" and city = "Albany, TX". This means every job in the RDD is in
Albany and mentioned java. Then, you can analyze each of the descriptions for
these jobs to find correlated words to "java", then output a Row in a new RDD
for each of these correlated words as well as some measure of how correlated
they are.

"""

from pyspark.ml.feature import CountVectorizer, IDF

def map_to_words(job_row):
    result = {}
    words = job_row.words
    freqs = job_row.features.toArray().tolist()
    for i in range(len(words)):
        word = words[i]
        if word in result:
            result[word] += freqs[i]
        elif freqs[i] > 0.0:
            result[word] = freqs[i]
    return result

def merge_dicts(dict1, dict2):
    result = {**dict1, **dict2}
    for k, v in result.items():
        if k in dict1 and k in dict2:
            result[k] = v if v > dict1[k] else dict1[k]
    return result
                

def get_correlation(frame, keyword, city):
    featurized = CountVectorizer(inputCol = "words", outputCol = "rawFeatures").fit(frame)
    freq_vectors = featurized.transform(frame)

    scaled = IDF(inputCol = "rawFeatures", outputCol = "features").fit(freq_vectors).transform(freq_vectors)

    job_vectors = scaled.select("words", "features").rdd
    worded_vectors = job_vectors.map(map_to_words)
    reduced_dict = worded_vectors.reduce(merge_dicts)
    print(reduced_dict)
