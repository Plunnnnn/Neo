from bs4 import BeautifulSoup
import requests
import json
import os
import mysql.connector
import re
import random
import os
import MySQLdb


query = """
        INSERT INTO movies (movieid, titre, lecteur_link, description, categories, poster, sortie, director, cast, time, ratings)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """


config = {
    'host': os.getenv("DATABASE_HOST"),
    'user': os.getenv("DATABASE_USERNAME"),
    'passwd': os.getenv("DATABASE_PASSWORD"),
    'db': os.getenv("DATABASE"),
    'autocommit': True,
    'ssl_mode': "VERIFY_IDENTITY",
    'ssl': {'ca': "F:\\Project\\Neo-main\\NeoStream\\cert\\cacert.pem"}  
}

connection = MySQLdb.connect(**config)

pattern = r"https?://papadustream\.tv/[^/]+/[^/]+\.html"
affiche = r"https://image\.tmdb\.org/t/p/w185/[^/]+\.jpg"
lecteur = r"https://lecteurvideo\.com/embed\.php\?id=[^/]+"


cursor = connection.cursor()
def doublon(l):
    fini = []
    for item in l:
        if item not in fini:
            fini.append(item)
    return fini


header = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    
}

def choix():
    titre = str(input())
    base_url = "https://papadustream.tv/?s=%s"
    requete =base_url % (titre)
    response = requests.get(requete, headers=header)

    choix_film = doublon(re.findall(pattern, response.text))
    affiche_film = doublon(re.findall(affiche, response.text))

    return(choix_film,affiche_film)

def rajout(liste):
    choix = int(input("quel film voulez vous ajouter ?"))
    film =liste(0)[choix-1]
    poster = liste(1)[choix-1]
    return (requests.get(film, headers=header), poster)

def informations(response, url, affiche):
    soup = BeautifulSoup(response.content , 'html.parser')
    titre = soup.find('h3').text
    
    desc = soup.find(class_='f-desc')
    
    p_tags = soup.find_all('p')
    for p_tag in p_tags:
        strong_tag = p_tag.find('strong')
        if strong_tag and strong_tag.text.strip() == 'Genre:':
            genre = p_tag.contents[-1].strip()
            break

    cast_list = soup.findAll(rel='tag')
    cast = []
    for act in cast_list:
        cast.append(act.text)
        
    reader = re.findall(lecteur, response.text)

    num = re.search(r'/(\d+)/', url)

    if num:
        id = num.group(1)
        
    
    return ()



    


cursor.close()
connection.close()




