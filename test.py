import requests
import os
from bs4 import BeautifulSoup
import time
import json
import sys

min = int(sys.argv[1])
counter = 0
max = min + 100
trigger = False
with open('jobs.json') as json_file:
    data = json.load(json_file)
    for jobs in data:
        if counter > max:
            break
        if counter > min:
            trigger = True
        if(trigger):
            r = requests.get(jobs['url'])
            soup = BeautifulSoup(r.text,features='html.parser')
            response = str(soup.find(id='jobDescriptionText'))
            f = open("descriptions/"+jobs['jobkey']+".html","a")
            f.write(response)
            f.close()
        counter = counter + 1

