# Flujo Git — Wally Grow

## Modelo

```
feat/fix/*  →  PR  →  develop  →  PR (release)  →  main
```

- **`main`:** producción / estable.
- **`develop`:** integración diaria.
- **Ramas de trabajo:** salen de `develop` y vuelven a `develop` por PR.

## Día a día

```powershell
git fetch origin
git checkout develop
git pull origin develop
git checkout -b feat/mi-cambio
# ... trabajo ...
git push -u origin HEAD
# Abrir PR hacia develop (no hacia main)
```

## Release a producción

1. PR `develop` → `main`.
2. Revisar diff completo desde el último release.
3. Merge solo cuando el dueño/equipo lo autorice.

## Protección de ramas (pedir al dueño del repo)

En GitHub → Settings → Branches, sugerido para `main` (y opcionalmente `develop`):

- Require a pull request before merging
- Require approvals (al menos 1 si hay más de un colaborador)
- Do not allow bypassing the above settings
- Restrict who can push (nadie con push directo a `main`)

Sin admin en el repo, un colaborador con `WRITE` no puede activarlo: debe hacerlo el owner.

## Qué no hacer

- Push directo a `main`
- Feature PR directo a `main` saltando `develop`
- Force push a `main` / `develop` sin pedido explícito del owner
