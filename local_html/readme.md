# Skills Advisor
See the readme in the parent directory for an explanation on how to use the site.
## _Database Design_

The Design for this database presented several tough challenges, that I had to approach in an different way than the normal SQL RDMS design.


## Cities

- List of supported cities
- Search is done on cityName, but the formatted name is used for cities in multiple states that share a name.
- ID is an important foreign key used in other tables

## Jobs

- Largest table containing every CS job in America
- Contains normal info like name, city (id), lat, lng, posted date
- Also has indeed unique id (job key), along with the description for the job already in html format.
 
## Keywords

- List of supported skills to search on
- The ids here are strange but will make sense, each entry has a power as 2 for its ID, i.e 1, 2, 4, 18, 16 etc

## Job_Keywords

- Many to Many linker
- Links each job to all the keywords that appear for that job, also includes the city.
- Useful for searches, to find all jobs in a city that contain the keywords entered by the user.

## Correlations

- The most complicated table to figure out
- This table it meant to store the correlation data for each keyword in each city
- The correlation data is; keywords X, Y, Z ... in city N are correalted to Keyword W in City O with a value of H (0-1)
- However the number of correlations can vary from 1 - N-1 (where N is the total number of keywords)
- For better explanation of the problem see - [This Post](https://dba.stackexchange.com/questions/291136/best-way-to-store-correlation-data-for-searches)
- The solution I came up with is to sum all the keywords the user is searching on, and create a unique number, hence the odd keyword ids.
- By giving each keyword an id of a power of 2 it creates a bitmap, where adding a keyword "sets" it in the map