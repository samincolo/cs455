# Skill Advisor

Site now live at https://skilladvisor.net

The source code and all helper scripts and notebooks for the Skill Advisor
project.  Includes independent data retrieval code, Spark analysis code, and
web frontend code.

## Written by

* Shaunak Amin
* JeanMarc Ruffalo-Burgat
* Pierce Smith

# Web Frontend
- Enter the city in which you wish to work
- Enter the (CS) skills you know (for optimal results enter 1-4)
- The site will tell you want other languages you should learn based on what you know and what is seen in job postings in the area you wish to work.
- An example would be, if you entered the city Fort Collins, and the skills: Java and Javascript. You would find in job postings in that area that include both of those keywords the most common keyword to also appear is Python
- The site will also map out all the jobs in that city that include the keywords you entered, with a link and job description.

## Usage
- mount data files using `hadoop fs -put`
- navigate to root project directory
- edit `run_on_cluster.sh` to reflect the mounted data files
- execute `./run_on_cluster.sh`

More details in the Spark Analytics section.

# Dataset Creation
data.json, 1.17gb, ~200k jobs

## Source code
- Scripts and Jupyter notebooks used to perform the following tasks can be
  found in `/scraping.`

## Grab jobs off Indeed
- Indeed provides a public api to query for jobs
- A query was constructed with variables for the query type and city
- Using the JN `Scraping? Indeed! we loop over every programming language and city and query the api
- The jobs were de-duped using the jobkey value see Unique Job Keys JP
- The api only returns a snippet of the description so we would need to query the indeed site directly to grab this data

## Grab descriptions of jobs
- Indeed's api does not RL requests, so grabbing jobs was relatively simple however the Indeed site sent capcthas to any IP that sent 100 consecutive requests
- If requests were limited to 1 per 10s this bypassed the 100 request/ip limit but would be too slow.
- Using nordvpn's command line abilities, a bash script was created to send 100 requests to grab the job descriptions then change the vpn connection
- A list of avaible nordvpn servers can be found at https://api.nordvpn.com/server
- The bash script can be found at script.sh which called the python file test.py
- This new approach was much faster and all 200k job descriptions were grabbed in ~28 hours (compared to 504)

## Completing the dataset
- To complete the dataset the job descriptions would need to be added to the json grabbed from the api
- This simple proccess was completed in the JP Add desc to jobs
- The resulting dataset was 1.17 gb of ~200k CS jobs from around the world


# Spark Analytics

## Source code

The source code for all Spark analytics is found within the `src` folder.
Analysis was done using PySpark and the PySpark SQL / PySpark MLLib libraries.

- `run.py`: This is the main script that is submitted to the cluster by the
   `run_on_cluster.sh` script.
- `launch_job.py`: This module loads data in from HDFS, processes and persists
   this data (including tokenizing job descriptions), then runs a correlation
   task for every city in the dataset.
- `sa_formatter.py`: This module is used to format and tokenize the job
   descriptions. Punctuation is removed, words are set to lowercase, text is
   split on spaces, and then everything is trimmed to a subset of keywords.
- `correlator.py`: This module performs FP-Growth on formatted job descriptions,
   outputting a dataframe full of correlations that is later saved as a .csv by
   the `launch_job.py` script.

## Usage

To run this on your own cluster, first mount the data, including the Indeed
jobs dataset, list of cities, and list of langauges, to HDFS.
`run_on_cluster.sh` should then be edited to reflect where you mounted each
file. By default, all data should be mounted under `/home/sa`, with the
following names:

- job dataset: `indeed_data_1.json`
- city file: `top_citities.txt` (yes, unfortunate typo)
- keyword file: `languages.txt`

When the job is finished, you will find the output .csv files, one for each
city in the city file, within `/home/sa/out_b_csv`.


## Source code

All source code for the website frontend can be found in `/local_html`.
