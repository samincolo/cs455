#! /bin/bash

source_dir=src
hdfs_data_dir=/home/sa

run_script="$source_dir/run.py"
data_files="$hdfs_data_dir/indeed_data_1.json"
city_file="$hdfs_data_dir/cities-with-state-code.txt"
kw_file="$hdfs_data_dir/languages.txt"

$SPARK_HOME/bin/spark-submit --py-files `stat --format %n $source_dir/*.py | paste -d, -s` $run_script $data_files $city_file $kw_file
