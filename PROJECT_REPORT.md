# Movie Collection System - Project Report

**Student ID:** 2439673  
**Course:** Web Development  
**Date:** April 2026

---

## Introduction

For this assignment, I developed a movie collection management system that lets users track their personal movie library. The idea came from wanting to organize my own movie collection better, and I thought it would make a practical project that demonstrates all the required features.

The system is built using PHP and MySQL on the backend, with Twig templates for the frontend. I chose this stack because it's what we covered in class and I wanted to get more comfortable with server-side programming.

---

## Project Overview

The application allows users to:
- Add new movies to their collection with detailed information
- View all movies in a nice grid layout with filtering options
- Edit existing movie entries when information changes
- Delete movies they no longer own
- Search through their collection using multiple criteria at once
- Register new accounts with CAPTCHA verification
- Securely log in to access their personal collection

I tried to make the interface clean and modern with a dark theme, which I think looks better than the typical bright white backgrounds you see everywhere.

---

## Features Implemented

### 1. CRUD Operations

Getting all four CRUD operations working was actually more challenging than I expected. Here's what I implemented:

**Create (Add Movies):**
The add movie form has 16 different fields covering everything from basic info like title and genre to more specific details like whether it's available in 4K or if it has a sequel. I added validation to make sure users can't submit incomplete data - for example, the title is required, and ratings have to be between 0 and 10.

One issue I ran into was with the database parameter binding. I kept getting errors about type mismatches, and it took me a while to figure out that my bind_param string didn't match the actual number of parameters I was passing. Once I counted them properly (16 parameters need 16 type characters), it worked fine.

**Read (View Movies):**
The main dashboard shows all movies in a grid layout. I added statistics at the top showing total movies, average rating, and total runtime, which I thought would be useful for users to see at a glance. Each movie card displays the key information and uses color-coded badges for the watch status.

I also created a detailed view page where you can see all the information about a specific movie. This was pretty straightforward using prepared statements to fetch the data by ID.

**Update (Edit Movies):**
The edit functionality pre-fills the form with existing data, which makes it easy to change just one or two fields without retyping everything. I reused a lot of the validation logic from the add page to keep things consistent.

**Delete:**
For deletion, I added a JavaScript confirmation dialog so users don't accidentally delete movies. The actual deletion is handled by a simple PHP script that removes the entry from the database.

### 2. Security Implementation

Security was a major focus since we learned about common vulnerabilities in class. I implemented all five required features:

**Input Filtering:**
I used `filter_input()` for GET parameters and `trim()` on all text inputs to remove extra whitespace. For numeric fields like year and rating, I validate that they're actually numbers and within reasonable ranges. All database queries use prepared statements with parameterized queries, which prevents SQL injection attacks.

**Output Escaping:**
I'm using Twig templates throughout the site, and Twig automatically escapes output by default. This means any user-submitted content gets properly escaped before being displayed, preventing XSS attacks. For the Ajax responses, I manually apply `htmlspecialchars()` to make sure everything is safe.

**Session Protection:**
Every page except login and register checks if the user is logged in by verifying `$_SESSION['user_id']`. If someone tries to access a protected page without logging in, they get redirected to the login page. I also configured the session to use a secure path.

**CAPTCHA:**
The registration page includes a visual CAPTCHA that generates a random 6-character code with some noise and distortion to prevent bots. It's stored in the session and validated when the form is submitted. I had to make sure the GD library was enabled for the image generation to work.

**Password Encryption:**
All passwords are hashed using `password_hash()` with the PASSWORD_DEFAULT algorithm, which currently uses bcrypt. When users log in, I use `password_verify()` to check if the password matches. The passwords are never stored in plain text, which is crucial for security.

### 3. Search Functionality

The search system supports multiple simultaneous criteria, which was one of the more complex parts. On the main page, users can filter by:
- Text search (searches across title, description, director, and cast)
- Genre dropdown
- Director name
- Watch status
- Release year

All these filters work together, so you can search for "action movies from 2019 that you've already watched" for example. I built the SQL query dynamically based on which filters are active, using prepared statements to keep it secure.

### 4. Advanced Ajax Search

The advanced search page uses Ajax to provide a better user experience. Instead of reloading the page every time you change a filter, it sends a JSON request to the server and updates the results dynamically.

The Ajax search supports even more criteria than the basic search:
- Title, genre, director, language
- Release year
- Rating range (min and max)
- Watch status
- Boolean filters for favourites, sequels, and 4K movies
- Sorting options

I spent quite a bit of time getting the JavaScript right. The fetch API sends a POST request with JSON data, and the PHP backend returns the results as JSON. Then JavaScript builds the HTML for each movie card and inserts it into the page. It feels much more responsive than traditional form submissions.

### 5. Template Engine

I used Twig for all the pages, which made the code much cleaner. Instead of mixing PHP and HTML everywhere, I can keep the logic in PHP files and just pass data to the templates.

The base template has all the common elements like the navigation bar and footer, and then each page extends it and fills in the content block. This means if I want to change the navigation menu, I only have to edit one file instead of eight.

Twig's auto-escaping is also great for security since I don't have to remember to manually escape every variable.

---

## Technical Challenges

### Database Connection Issues
Initially, I had the wrong password in the database configuration file, which caused connection errors. It took me a bit to realize the password needed to match my phpMyAdmin credentials.

### Twig Installation
Getting Twig installed was probably the biggest headache. Composer had proxy issues on my machine, and I had to clear the proxy environment variables to get it to download the packages. Then I had to make sure the vendor folder was uploaded to the server with the correct permissions.

### Parameter Binding Bug
The most frustrating bug was in the add and edit movie functions. My bind_param type string had the wrong number of characters, which caused cryptic errors. I had to carefully count each parameter and match it to the correct type (s for string, i for integer, d for decimal). Once I got the count right - 16 parameters for insert, 17 for update (including the movie ID) - everything worked.

### File Permissions
On the server, I ran into permission denied errors with the vendor folder. I had to set the permissions to 755 recursively so PHP could read the Twig files.

---

## Testing

I tested the application manually by:
- Creating several test movies with different data
- Editing movies and verifying changes saved correctly
- Deleting movies and confirming they were removed
- Testing all search filters individually and in combination
- Trying to access protected pages without logging in
- Registering new accounts with correct and incorrect CAPTCHA codes
- Testing with invalid input (negative ratings, future years, etc.)

For security testing, I could use tools like OWASP ZAP or Burp Suite to scan for vulnerabilities. The prepared statements should prevent SQL injection, and the Twig escaping should prevent XSS attacks.

---

## What I Learned

This project really helped me understand how web applications work end-to-end. Some key takeaways:

1. **Security is hard:** It's not enough to just make things work - you have to think about all the ways users (or attackers) might misuse your application. Input validation, output escaping, and proper authentication are essential.

2. **Debugging takes time:** A lot of my time was spent tracking down bugs rather than writing new code. The bind_param issue taught me to be more careful about counting parameters and matching types.

3. **Template engines are worth it:** Twig made the code so much cleaner and easier to maintain. Separating logic from presentation is a good practice.

4. **Ajax improves user experience:** The advanced search feels much more modern and responsive than traditional page reloads. Users expect this kind of interactivity nowadays.

5. **Documentation matters:** Writing clear comments and keeping track of what each file does made it easier to come back to the code after a break.

---

## Future Improvements

If I had more time, I would add:
- Image upload for movie posters
- User profiles with customizable settings
- Export functionality to save the collection as CSV or PDF
- Movie recommendations based on what you've watched
- Social features to share collections with friends
- Better mobile responsiveness
- More advanced statistics and charts

---

## Conclusion

Overall, I'm happy with how the project turned out. It meets all the requirements from the rubric and includes some extra features like the statistics dashboard and advanced Ajax search. The biggest lesson was that web development involves a lot more than just writing code - you have to think about security, user experience, deployment, and maintenance.

The project gave me hands-on experience with PHP, MySQL, Twig, Ajax, and web security concepts. I feel much more confident building web applications now than I did at the start of the course.

---

## References

- PHP Documentation: https://www.php.net/docs.php
- Twig Documentation: https://twig.symfony.com/doc/
- OWASP Security Guidelines: https://owasp.org/
- MySQL Documentation: https://dev.mysql.com/doc/
- Course lecture notes and lab materials
