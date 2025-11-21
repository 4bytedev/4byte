
# 4Byte

[4byte.dev](https://4byte.dev) websitesi kaynak kodları.


## Tech Stack

**Client:** React, Inertia, Redux, TailwindCSS

**Server:** Laravel

**Recommendation:** Gorse

**Cache:** Redis


## Installation

Install && run project with docker-compose

```bash
wget https://raw.githubusercontent.com/4bytedev/4byte.dev/refs/heads/main/docker-compose.yml
wget https://raw.githubusercontent.com/4bytedev/4byte.dev/refs/heads/main/.env.example -O .env

# Start Docker containers
docker compose --env-file .env --profile app --profile production up -d
```

## Local Development

Clone the repository

```bash
git clone https://github.com/4bytedev/4byte.dev.git
```

Preapre local development data

```bash
make migrate
make create-permissions
make seed
```

Now, you can login /admin page with the following credentials

admin@example.com
password

## Authors

- Open source community


## License

[GPL](https://choosealicense.com/licenses/gpl-3.0/)
