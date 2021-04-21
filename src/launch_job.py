"""
launch_job.py

Handles creating the spark session, loading the data, and passing it to the
correlator. 
"""

import correlator
import pyspark
from pyspark.sql import SparkSession

def launch(parameters):
    spark = get_spark_session()
    data_frames = []

    data = combine_data(list(map(lambda fn: get_json_data_frame(fn, spark), parameters["data_files"])))
    cities = get_txt_rdd(parameters["city_file"], spark).collect()
    keywords = get_txt_rdd(parameters["keyword_file"], spark).collect()

    for city in cities:
        city_split = city.split(",")
        city_name = city_split[0].strip()
        state_code = city_split[1].strip()

        print(city_name)
        print(state_code)

        city_frame = data.filter((data.city == city_name) & (data.state == state_code))
        for keyword in keywords:
            final_frame = city_frame.filter(city_frame.description.like("%{}%".format(keyword)))
            print(final_frame.show(1))
            result = correlator.get_correlation(final_frame.rdd, keyword, city)

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

