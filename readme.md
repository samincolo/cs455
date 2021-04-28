# Dataset Creation
data.json, 1.17gb, ~200k jobs

## Usage
- navigate to root project directory
- `hadoop fs -put /data /home/sa`
- `./run_on_cluster.sh`


## Grab jobs off Indeed

- Indeed provides a public api to query for jobs
- A query was constructed with variables for the query type and city
- Using the JN Scraping? Indeed! we loop over every programming language and city and query the api
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
