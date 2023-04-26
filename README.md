# BookStore APIs
## Built with PHP Laravel Framework

## 1. import-books/{quantity}

This API is used for importing book records from a given third-party API. Records are organized in a way that suits Database design. Database Normalization is implemented with a level of 3NF. Books, Authors, Genres, and Publishers are divided into different tables to prevent data duplicity and redundancy. The parameter "quantity" decides how many records we want to fetch from the third-party API.

## 2. Admin/Login

This API is for authenticating the credible user and providing access to the Admin panel where he/she can perform Add/Edit/Delete operations with book records. Sanctum-based token authentication is used that is suitable for frontend frameworks. API-based authentication is used instead of "SPA authentication", which is an inbuilt feature of Sanctum.

## 3. book

For fetching the records of books based on different criteria such as searched values filtered by Title, Author, Genre, ISBN, Publication date and Publisher. Pagination is done on the APIs level not with JS libraries. Hence, we are getting only requested data as per pagination count not all records at once. As per the requirement, only the Title field would be visible. Hence, Sorting can be done on the Title field only.

For queries to the database, Eloquent and joins are used. Although the necessary data is distributed in multiple tables, SQL queries would be complex. Hence, I haven't tried ElasticSearch as suggested in the Assessment document.
## 4. book/{id}

For fetching singular book records based on the primary key of the book. The eloquent ORM concept is used for fetching the record by building relations of Authors, Books, Genres, and Publishers.

## 5. admin/book (POST)

For creating a new Book record this API gets called. As we need to modify multiple tables, To follow ACID Properties, the transaction is used.

## 6. admin/book/{id} (PATCH)

For updating existing records this API is called. To follow ACID Properties, the transaction is used.

## 7. admin/book/{id} (DELETE)

For deleting existing records this API is called.


# Performance

1. For reducing database query execution time, indexing is implemented on suitable fields in the books table. The fields are title, author, ISBN, genre, publication date, and publisher. Query execution time with 300 records with the such requirement is around 500ms for all kinds of database operations.
2. HTTP cache headers or Redis caching is not implemented, because there are no static records in the system that remains the same for a long.
3. Elastic search is not used due to incompatibility with the database design.

# Technology

1. Laravel 9
2. PHP 8.1
3. MariaDB 10
4. Sanctum Authentication
