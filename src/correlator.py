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
def get_correlation(rdd, keyword, city):
    pass
