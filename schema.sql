-- spoty-goal main database schema (PostgreSQL)

DROP TABLE IF EXISTS public.tokens;
DROP TABLE IF EXISTS public.users;
DROP TABLE IF EXISTS public.sessions;

CREATE TABLE public.tokens (
    token_id INT GENERATED ALWAYS AS IDENTITY,
    access_token VARCHAR NOT NULL,
    refresh_token VARCHAR NOT NULL,
    expire_date TIMESTAMP NOT NULL,
    PRIMARY KEY (token_id)
);

CREATE TABLE public.users (
    user_id INT GENERATED ALWAYS AS IDENTITY,
    token_id INT,
    username VARCHAR NOT NULL,
    PRIMARY KEY (user_id),
    CONSTRAINT fk_token
        FOREIGN KEY (token_id)
            REFERENCES tokens (token_id)
                ON DELETE CASCADE
);

CREATE TABLE public.sessions (
    session_id VARCHAR NOT NULL,
    user_id INT NOT NULL,
    PRIMARY KEY (session_id),
    CONSTRAINT fk_user
        FOREIGN KEY (user_id)
            REFERENCES users (user_id)
                ON DELETE CASCADE
);
