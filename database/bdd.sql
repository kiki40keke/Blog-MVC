create table post(
    id int unsigned not null primary key auto_increment,
    name varchar(255) not null,
    slug varchar(255) not null,
    content text not null,
    created_at datetime not null,
    image varchar(255) null
)

create table category(
    id int unsigned not null primary key auto_increment,
    name varchar(255) not null,
    slug varchar(255) not null
)
create table post_category(
    post_id int unsigned not null,
    category_id int unsigned not null,
    primary key(post_id, category_id),
    foreign key(post_id) references post(id) on delete cascade on update restrict,
    foreign key(category_id) references category(id) on delete cascade on update restrict
)

create table user(
    id int unsigned not null primary key auto_increment,
    username varchar(50) not null,
    password varchar(255) not null
)

insert into category(name, slug) values
('Programmation', 'programmation'),
('Voyages', 'voyages'),
('Musique', 'musique'),
('Cinéma', 'cinema'),
('Sport', 'sport');

insert into post(name, slug, content, created_at) values
('Mon premier post', 'mon-premier-post', 'Contenu de mon premier post', NOW()),
('Mon deuxième post', 'mon-deuxieme-post', 'Contenu de mon deuxième post', NOW()),
('Mon troisième post', 'mon-troisieme-post', 'Contenu de mon troisième post', NOW());

insert into post_category(post_id, category_id) values
(1, 1),
(1, 2),
(2, 3),
(3, 4),
(3, 5);

select * from post_category pc
left join category c on pc.category_id = c.id
where pc.post_id = 1;


SELECT c.*,pc.post_id
FROM post_category pc
JOIN category c ON c.id=pc.category_id
WHERE pc.post_id IN (6,4,11,20);

ALTER TABLE post
ADD image VARCHAR(255) NULL AFTER created_at;
