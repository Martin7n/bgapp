terraform {
  required_providers {
    docker = {
      source  = "kreuzwerker/docker"
      version = "~> 3.0"
    }
  }
}

provider "docker" {}

# Create a custom Docker network so containers can communicate by name
resource "docker_network" "bgapp_network" {
  name = "bgapp_net"
}

# Build DB image from Dockerfile.db in root (teraf/)
resource "docker_image" "db_image" {
  name = "bgapp-db-local"
  build {
    context    = "${path.module}/.."               # teraf/
    dockerfile = "${path.module}/../Dockerfile.db" # teraf/Dockerfile.db
  }
}

# Run the DB container on bgapp_net network
resource "docker_container" "db_container" {
  name  = "bgapp-db"
  image = docker_image.db_image.name
  networks_advanced {
    name = docker_network.bgapp_network.name
  }
  ports {
    internal = 3306
    external = 3306
  }
  env = [
    "MYSQL_ROOT_PASSWORD=bgapp123",
    "MYSQL_DATABASE=bgapp"
  ]
  restart = "unless-stopped"
}

# Build Web image from Dockerfile.web inside teraf/tform
resource "docker_image" "web_image" {
  name = "bgapp-web-local"
  build {
    context    = "${path.module}/.."               # teraf/
    dockerfile = "${path.module}/Dockerfile.web"   # teraf/tform/Dockerfile.web
  }
}

# Run Web container on the same network, depends on DB container to be ready
resource "docker_container" "web_container" {
  name  = "bgapp-web"
  image = docker_image.web_image.name
  networks_advanced {
    name = docker_network.bgapp_network.name
  }
  ports {
    internal = 80
    external = 8080
  }
  depends_on = [docker_container.db_container]
env = [
  "DB_HOST=bgapp-db",
  "DB_NAME=bulgaria",          # ← must match DB name from SQL
  "DB_USER=web_user",          # ← must match GRANT statement
  "DB_PASS=Password1"          # ← must match GRANT statement
]

  restart = "unless-stopped"
}


