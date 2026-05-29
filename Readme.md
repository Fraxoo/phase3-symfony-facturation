# Facturation (Symfony) — Démarrage avec Docker

Ce projet est prévu pour tourner via **Docker Compose** (FrankenPHP + Caddy).

## Prérequis

- Docker + Docker Compose (`docker compose`)

## Lancer le projet (dev)

Depuis la racine du projet :

```bash
docker compose up --build --wait
```

Puis ouvre :

- Application : `http://localhost/` (ou `https://localhost/`)
- Mailpit (emails) : `http://localhost:8025/`
- Gotenberg (PDF) : `http://localhost:3000/`

> Remarque : en HTTPS, ton navigateur peut afficher un avertissement (certificat local).

## Arrêter

```bash
docker compose down
```

Pour supprimer aussi les volumes (reset complet, données incluses) :

```bash
docker compose down -v
```

