# laravel-dependencies

A set of laravel dependencies for development and deployment.

## Dependencies
- [docker](https://docs.docker.com/desktop/setup/install/linux/)
- [nix](https://nixos.org/download/) (if you want to run a development shell)
- [caddy-reverse-proxy](https://github.com/Mik-813/caddy-reverse-proxy) (reverse proxy for deployment)

## Develop
```bash
nix develop
```

## Deploy
Create `.env`
```bash
cp .env.example .env
```
Edit `.env` variables
```bash
docker compose up -d
```
