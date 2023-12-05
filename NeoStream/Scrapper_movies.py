from bs4 import BeautifulSoup
import requests
import json
import mysql.connector

# Your database query
query = """
        INSERT INTO movies (movieid, titre, lecteur_link, description, categories, poster, sortie, director, cast, time, ratings)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """

# Database connection parameters
config = {
    'user': 'basmoussent',
    'password': 'zsedLOKI:123!',
    'host': 'mediadb.czpusmrdxalt.eu-north-1.rds.amazonaws.com',
    'database': 'MovieDB',
}

# Connect to the database
conn = mysql.connector.connect(**config)
cursor = conn.cursor()

# URL and headers for IMDb scraping
header = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
}
url = "https://www.imdb.com/chart/top/?ref_=nv_mv_250"

# Get movie names from IMDb
response = requests.get(url, headers=header)
soup = BeautifulSoup(response.content, 'html.parser')
movie_names_with_numbers = [fname.text for fname in soup.find_all(class_='ipc-title__text')]
movie_names = [name.split('. ', 1)[1] if '. ' in name else name for name in movie_names_with_numbers]

# Base URLs for movie data
base_url = "https://kstream.net/search/"
movie_info_url = "https://kstream.net/movie/"

# Loop through movie names
for film in movie_names:
    url_post = base_url + str(film)
    response = requests.get(url_post)
    
    if response.status_code == 200:
        data_tuple = ()  # Use a different variable for the tuple
        data = response.json()
        results = data.get("results", [])
        
        for result in results:
            movie_id = result.get("Id")
            lecteur_link = "https://vidsrc.to/embed/movie/" + str(movie_id)
            lecteur_check = requests.get(lecteur_link)
            movie = requests.get(movie_info_url + str(movie_id))
            
            print(movie.content, "\n")

            cursor.execute("SELECT COUNT(*) FROM movies WHERE movieid = %s", (movie_id,))
            count = cursor.fetchone()[0]

            if count > 0:
                
                continue


            if lecteur_check.status_code == 200 and movie.status_code == 200:
                movie_data = json.loads(movie.content)
                
                # Update data_tuple as a tuple
                data_tuple = (
                    movie_id,
                    movie_data['Title'],
                    lecteur_link,
                    movie_data['Overview'],
                    movie_data['Genres'],
                    movie_data['Poster'],
                    movie_data['Release'],
                    movie_data['Director'],
                    movie_data['Cast'],
                    movie_data['Runtime'],
                    movie_data['Vote']
                )
                print("ici")
            elif lecteur_check.status_code == 200:
                poster_url = result.get("Poster")
                title = result.get("Title")
                year = result.get("Year")
                
                # Update data_tuple as a tuple
                data_tuple = (
                    movie_id,
                    title,
                    lecteur_link,
                    "Description cannot be retrieved",
                    "Categories cannot be retrieved",
                    poster_url,
                    year,
                    "Director cannot be retrieved",
                    "Cast cannot be retrieved",
                    "Movie time cannot be retrieved",
                    0
                )
                print("la")
            else:
                continue

            # Execute the insertion query
            cursor.execute(query, data_tuple)

            # Commit the transaction
            conn.commit()

# Close the cursor and connection
cursor.close()
conn.close()