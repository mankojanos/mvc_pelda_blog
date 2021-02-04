CREATE TABLE userek(
    usernev VARCHAR(50) PRIMARY KEY,
    password VARCHAR(100)
);

CREATE TABLE posztok(
    id INT AUTO_INCREMENT PRIMARY KEY,
    cim VARCHAR(100),
    tartalom TEXT,
    szerzo VARCHAR(100) NOT NULL,
    FOREIGN KEY (szerzo) REFERENCES userek(usernev)
);

CREATE TABLE kommentek(
    id INT AUTO_INCREMENT PRIMARY KEY,
    tartalom TEXT,
    szerzo VARCHAR(100) NOT NULL,
    poszt INT NOT NULL,
    FOREIGN KEY(szerzo) REFERENCES userek(usernev),
    FOREIGN KEY(poszt) REFERENCES posztok(id)
)
