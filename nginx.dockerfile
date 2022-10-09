# FROM nginx:stable-alpine
FROM opentracing/nginx-opentracing:latest-alpine

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

# MacOS staff group's gid is 20, so is the dialout group in alpine linux. We're not using it, let's just remove it.
RUN delgroup dialout

# RUN addgroup -g ${GID} --system laravel
# RUN addgroup --gid ${GID} --system laravel
# RUN adduser -G laravel --system -D -s /bin/sh -u ${UID} laravel
# RUN adduser --group laravel --system -D -s /bin/sh -u ${UID} laravel
# RUN sed -i "s/user  nginx/user laravel/g" /etc/nginx/nginx.conf

ADD ./nginx/default.conf /etc/nginx/conf.d/
COPY ./nginx/nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p /home/project
RUN mkdir -p /home/project/public

# RUN wget https://github.com/jaegertracing/jaeger-client-cpp/releases/download/v0.4.2/libjaegertracing_plugin.linux_amd64.so -O /usr/local/lib/libjaegertracing_plugin.so

COPY ./nginx/jaeger-config.json /etc/jaeger/jaeger-config.json
