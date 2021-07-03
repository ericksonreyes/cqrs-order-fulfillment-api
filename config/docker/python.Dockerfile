FROM python:3.7.11-alpine3.13

WORKDIR /var/www/html

COPY . .

ENV VIRTUAL_ENV=/opt/venv

RUN python3 -m venv $VIRTUAL_ENV

ENV PATH="$VIRTUAL_ENV/bin:$PATH"

RUN python3 -m pip install --upgrade pip

RUN pip install pika

RUN pip install pyyaml

RUN pip install python-dotenv

ENTRYPOINT [ "python" ]