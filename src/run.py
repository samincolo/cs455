"""
run.py

This is the script sent to Spark. You need to provide it with three arguments:
a comma separated list of the names of each data file, the name of the file to
read cities from, and the name of the file to read keywords (languages) from.
All of these files are hdfs paths.

"""

import sys
import launch_job

def run():
    if len(sys.argv) != 4:
        raise Exception("Expected 3 arguments: got {}".format(len(sys.argv) - 1))

    parameters = {
        "data_files": sys.argv[1].split(","),
        "city_file": sys.argv[2],
        "keyword_file": sys.argv[3]
    }

    launch_job.launch(parameters)

run()
