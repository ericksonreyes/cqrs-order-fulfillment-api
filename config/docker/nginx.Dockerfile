FROM nginx:stable-alpine

WORKDIR /etc/nginx/conf.d

COPY config/docker/config/nginx/docker.nginx.conf .

EXPOSE 8000

RUN mv docker.nginx.conf default.conf && chown -R nobody:nobody /var/log/nginx