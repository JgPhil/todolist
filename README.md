ToDoList
========

Projet 8 du parcours DA PHP/Symfony de OpenClassrooms
- lien fichier Git : https://github.com/webyves/todolist

# Installation Notes (PAR SSH)
1) Cloner le repository sur votre serveur
3) Utiliser composer pour installer et mettre a jour les composant avec la commande 
	- composer install
2) Mettre a jour le fichier parameters.yml (situé dans app/config) 
	- avec les informations de votre base de donnée
	- avec les informations de votre configuration d'envoie d'email
	- si le fichier n'existe pas copier le fichier parameters.yml.dist en le renomant parameters.yml puis modifiez le.
4) Créer la base de donnée et mettez la a jour avec les commandes
	- php bin/console doctrine:database:create
	- php bin/console doctrine:schema:update --force
5) envoyer les fixtures
	- php bin/console doctrine:fixtures:load

# Comment contribuer 
Si vous souhaitez apporter vos ressources a ce projet rendez vous sur le liens suivant pour savoir comment faire : 
https://github.com/webyves/todolist/blob/master/CONTRIBUTING.md

# Patch Notes
