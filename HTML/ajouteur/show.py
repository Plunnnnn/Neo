from flask import Flask, render_template, request
from bs4 import BeautifulSoup
import requests
import mysql.connector
import re
import random
import os
from dotenv import load_dotenv

env  = 'F:\\Project\\Neo-main\\NeoStream\\.env'
load_dotenv(env)

query = """
        INSERT INTO movies (movieid, titre, lecteur_link, description, categories, poster, sortie, director, cast, time, ratings)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """


config = {
    'host': os.getenv("DATABASE_HOST"),
    'user': os.getenv("DATABASE_USERNAME"),
    'passwd': os.getenv("DATABASE_PASSWORD"),
    'db': os.getenv("DATABASE"),

}

connection = mysql.connector.connect(**config)
cursor = connection.cursor()

pattern = r"https?://papadustream\.tv/[^/]+/[^/]+\.html"
affiche = r"https://image\.tmdb\.org/t/p/w185/[^/]+\.jpg"   # patter regex pour la reconnaissance
lecteur = r"https://lecteurvideo\.com/embed\.php\?id=[^/]+"
app = Flask(__name__)

@app.route('/', methods=['GET', 'POST'])
def choix():
    global affiche_film, choix_film
    if request.method == 'POST':
        titre = request.form['titre']
        app.config['titre'] = titre
        base_url = "https://papadustream.tv/?s=%s"
        requete = base_url % (titre)
        header = {'User-Agent': 'Mozilla/5.0'}                        #choix du film + recuperation de la page web avec le contenue
        response = requests.get(requete, headers=header)
        choix_film = doublon(re.findall(pattern, response.text))
        affiche_film = doublon(re.findall(affiche, response.text))
        
        return render_template('affiche.html', affiche_film=affiche_film)

    return render_template('index.html')

def doublon(l):
    fini = []
    for item in l:
        if item not in fini:
            fini.append(item)
    return fini


def informations(response, url, affiche):
    soup = BeautifulSoup(response.content , 'html.parser')
    titre = soup.find('h3').text
    
    desc = soup.find(class_='f-desc').text
    
    p_tags = soup.find_all('p')
    for p_tag in p_tags:
        strong_tag = p_tag.find('strong')
        if strong_tag and strong_tag.text.strip() == 'Genre:':          # recupere tout les infos du film necessaire pour ajouter a la DB
            genre = p_tag.contents[-1].strip()
            break

    cast_list = soup.findAll(rel='tag')
    cast = []
    for act in cast_list:
        cast.append(act.text)
        
    lecteur = soup.find('iframe')

    
    if lecteur:
        reader = lecteur['src']
    
    castt =f"{', '.join(cast)}"
        

    match = re.search(r'\d+', reader)
    id = match.group()
        
       
    data_tuple = (id, titre, reader, desc, genre, affiche, 'jsp', 'jsp', castt, 'jsp', random.randint(0,10))
    print(data_tuple)
    cursor.execute(query, data_tuple)
    connection.commit()
    print(reader)
    return reader

@app.route('/choose_movie', methods=['POST'])
def choose_movie():
    movie_number = int(request.form['titre'])
    
    
    
    response = requests.get(choix_film[movie_number])
      

    link = informations(response, choix_film[movie_number], affiche_film[movie_number])  # Rajoute le films a la DB et créer la page pour redigerer 
    print(link)
    
    return render_template('redirect.html', redirect_link = link)

if __name__ == '__main__':
    app.run(debug=True)

cursor.close()
connection.close()
