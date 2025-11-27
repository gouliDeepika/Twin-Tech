Hyperlocal Emergency Alert Wall 

A simple, fast, community-driven platform where people can instantly post nearby emergencies (accidents, fires, medical issues, etc.).

Project Overview

This project allows users to:

1.Post emergency alerts
2.Quickly understand severity through risk levels.
3.Help their community stay aware and safe
4.Admins can remove alerts

How It Works 

1.User posts an alert using a quick form

2.Backend (PHP + MySQL) stores alert in the database

3.Alert instantly appears on the Live Alert Wall

4.Public can see all alerts — no login needed

5.Admin dashboard lets admins delete irrelavent alerts

 Features

1.Public Users
2.Post emergency alerts
3.Risk Levels(Low / Medium / High)
4.Category tags (Accident, Fire, Medical, Other)
5.Simple and clean UI

Admin Panel
1.View all alerts
2.Delete irrelevant alerts

Tech Stack

1.Frontend	HTML, CSS
2.Backend	PHP
3.Database	MySQL
4.Server	XAMPP / Apache


📁 Project Structure
Twin-Tech/
│
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── delete_alert.php
│   └── logout.php
│
├── assets/
│   └── css/
│       └── style.css
│
├── index.php
├── config.php
├── submit_alert.php
└── favicon.ico

📌 Database Structure

Database Name: twin_tech

1. alerts Table
  
Column	Type
id	INT (PK)
title	VARCHAR
description	TEXT
area	VARCHAR
category	ENUM
severity	ENUM
created_at	TIMESTAMP

2. admins Table
   
Column	Type
id	INT (PK)
username	VARCHAR
password_hash	VARCHAR
created_at	TIMESTAMP

 Default Admin Login

Username: admin
Password: admin123

Why This Idea Is Unique?

1.Extremely simple but solves a real community problem
2.Hyperlocal — focused on nearby emergencies, not global
3.Lightweight and fast


Who Benefits?

Students

Local communities

Apartment residents

College campuses

Travelers

Traffic safety groups

Anyone who wants real-time awareness of nearby emergencies.
