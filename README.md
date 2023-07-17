# Cartola App

## Configurations


Up the containers
```bash
docker compose -f docker-compose-dev.yml up -d
```

Access the following container
```
docker exec -it cartola-app-php bash
```
And execute one of them:

```bash
round-result:register        Insert teams scores by round
round-result:remove          Remove scores of the last round
...
season:configure             Configure billing and quantity of excempt players by round
season:update-subscriptions  Add new teams to the current season
```

## Tests

```bash
php artisan test
```