
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
wget https://raw.githubusercontent.com/4bytedev/4byte/refs/heads/main/docker-compose.yml
wget https://raw.githubusercontent.com/4bytedev/4byte/refs/heads/main/.env.example -O .env

# Start Docker containers
docker compose --env-file --profile app --profile production up -d
```
## Authors

- Open source community


## License

[GPL](https://choosealicense.com/licenses/gpl-3.0/)
