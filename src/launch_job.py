"""
launch_job.py

Handles creating the spark session, loading the data, and passing it to the
correlator. 
"""

import correlator
from sa_formatter import SAFormatter
import pyspark
from pyspark.sql import SparkSession

def launch(parameters):
    spark = get_spark_session()
    data_frames = []

    data = combine_data(list(map(lambda fn: get_json_data_frame(fn, spark), parameters["data_files"])))
    cities = set(get_txt_rdd(parameters["city_file"], spark).collect())
    keywords = set(get_txt_rdd(parameters["keyword_file"], spark).collect())

    data_sets = {
        "data": data,
        "cities": cities,
        "keywords": keywords
    }

    launch_single(data_sets, "Fort Collins, CO", "java")

    """
    cities = get_txt_rdd(parameters["city_file"], spark).collect()
    keywords = get_txt_rdd(parameters["keyword_file"], spark).collect()

    for city in cities:
        city_split = city.split(",")
        city_name = city_split[0].strip()
        state_code = city_split[1].strip()

        city_frame = data.filter((data.city == city_name) & (data.state == state_code))
        city_frame.persist()
        for keyword in keywords:
            final_frame = city_frame
            #final_frame = city_frame.filter(city_frame.description.like("%{}%".format(keyword)))
            final_frame = sa_formatter.frame_to_words_frame(final_frame)
            result = correlator.get_correlation(final_frame, keyword, city)
    """

def get_spark_session():
    spark = SparkSession.builder.getOrCreate()
    return spark

def get_json_data_frame(filename, spark):
    df = spark.read.option("multiline", "true").json(filename)
    return df

def get_txt_rdd(filename, spark):
    rdd = spark.sparkContext.textFile(filename)
    return rdd

def combine_data(sources):
    if len(sources) < 2:
        return sources[0]

    last_source = sources[0]
    combined = None
    for source in sources[1:]:
        combined = last_source.union(source)
        last_source = source

    return combined

def launch_single(data_sets, city, keyword):
    data = data_sets["data"]
    keywords = data_sets["keywords"]

    city_split = city.split(",")
    city_name = city_split[0].strip()
    state_code = city_split[1].strip()

    formatter = SAFormatter()
    formatter.set_possible_keywords(keywords)

    city_frame = data.filter((data.city == city_name) & (data.state == state_code))
    final_frame = formatter.frame_to_words_frame(city_frame)
    final_frame = final_frame.select("words", "jobkey")
    result = correlator.get_correlation(final_frame, keyword, city)

