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

    cities = set(get_txt_rdd(parameters["city_file"], spark).collect())
    keywords = set(get_txt_rdd(parameters["keyword_file"], spark).collect())

    data = format_frame(combine_data(list(map(lambda fn: get_json_data_frame(fn, spark), parameters["data_files"]))), keywords)
    data.persist()

    data_sets = {
        "data": data,
        "cities": cities,
        "keywords": keywords
    }

    for city in cities:
        friendly_name = city.translate(str.maketrans(" ", "_"))
        launch_single(data_sets, city, spark).write.csv("/home/sa/out_b_csv/{}".format(friendly_name), header = True)

def format_frame(frame, keywords):
    formatter = SAFormatter()
    formatter.set_possible_keywords(keywords)

    frame = formatter.frame_to_words_frame(frame)
    frame = frame.select("words", "jobkey", "city", "state")

    return frame

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

def launch_single(data_sets, city, spark):
    data = data_sets["data"]
    keywords = data_sets["keywords"]

    city_split = city.split(",")
    city_name = city_split[0].strip()
    state_code = city_split[1].strip()

    frame = data.filter((data.city == city_name) & (data.state == state_code))

    return correlator.get_correlation(frame, keywords, city, spark)

