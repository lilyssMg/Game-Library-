# PRODUCT.md

## Product Name
Game Library

## Register
product

## Users
University students (CS program, Tamkang University). Using laptops in classrooms or dorms, daylight or indoor lighting. Task-focused: browse games, add or manage their own entries. Not power users; expect standard web UI patterns.

## Product Purpose
Group project: a shared game library where members can add, browse, edit, and delete video game entries. Secondary: display group member profiles. Runs offline on a Raspberry Pi on the local network.

## Tone
Functional and clean. No marketing copy. No decorative flair. The interface should disappear into the task.

## Anti-references
- Flashy gaming sites (neon, dark gamer aesthetic)
- SaaS dashboards with hero metrics
- Card grids with identical icon+heading+text blocks
- Any glassmorphism

## Strategic principles
- Offline-first: no CDN fonts, no external resources
- Simple HTML + CSS only, no JavaScript frameworks
- All pages share one stylesheet (style.css)
- PHP with PDO/MariaDB backend
