FROM mysql/mysql-server:5.7

ARG MYSQL_ALLOW_EMPTY_PASSWORD
ARG MYSQL_DATABASE
ARG MYSQL_USER
ARG MYSQL_PASSWORD
ARG SITE
ARG SITE_DIR
ARG DATABASE_DIR

# the regular dir is a volume so if we want to have things refresh on build we have to put the mysql data elsewhere
RUN rm -rf /var/lib/mysql2
RUN sed -i -e 's/datadir=\/var\/lib\/mysql/datadir=\/var\/lib\/mysql2/g' /etc/my.cnf
RUN echo "sql-mode=\"NO_ENGINE_SUBSTITUTION"\" >> /etc/my.cnf
COPY ${DATABASE_DIR}/database.sql.gz /tmp
RUN gunzip -c /tmp/database.sql.gz > /docker-entrypoint-initdb.d/database.sql
