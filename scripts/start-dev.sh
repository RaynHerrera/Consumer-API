#!/bin/bash

# Set the name of the Docker Compose file
COMPOSE_FILE=docker-compose.dev.yml

# Start the Docker Compose stack
echo "Starting Docker Compose stack..."
   docker-compose -f $COMPOSE_FILE up --build
echo "Docker Compose stack started."