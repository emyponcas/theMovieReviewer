# theMovieReviewer
by emyponcas developer.

theMovieReviewer es una aplicación web desarrollada con Symfony que permite descubrir películas, valorarlas, crear rankings personalizados por categorías y consultar estadísticas globales. El proyecto está enfocado al backend, priorizando una arquitectura sólida, control de acceso por roles y persistencia consistente de datos.

El sistema se integra con la API de TMDB para importar películas reales y construir una base de datos propia sobre la que los usuarios pueden interactuar.

---

## 🚀 Demo en vídeo

Puedes ver el funcionamiento completo aquí:  
https://youtu.be/zh7nHuUDId4

---

## 🧠 Qué permite hacer la aplicación

Los usuarios pueden explorar el catálogo de películas, filtrarlas por distintos criterios y dejar valoraciones usando un sistema de estrellas equivalente a una escala de 1 a 10. Cada usuario puede modificar sus valoraciones en cualquier momento y consultar su historial desde una sección dedicada.

También existe un sistema de categorías donde los usuarios pueden ordenar películas según su propio criterio, al estilo TierMaker, mediante un sistema de ranking por posiciones. A partir de estos rankings individuales, la aplicación genera automáticamente leaderboards globales basados en la media de posiciones de todos los usuarios.

El sistema distingue entre usuarios normales y administradores. El administrador dispone de un panel desde el que puede importar películas desde TMDB, crear y gestionar categorías, consultar usuarios registrados y visualizar estadísticas globales de la aplicación.

---

## 🔐 Seguridad y control de acceso

El proyecto implementa control de acceso mediante roles (`ROLE_USER` y `ROLE_ADMIN`).  
Las funcionalidades administrativas están completamente protegidas, tanto desde el backend como desde la configuración de seguridad de Symfony, evitando accesos no autorizados incluso si se intenta acceder manualmente por URL.

Además, se utiliza borrado lógico mediante el campo `isActive`, lo que permite desactivar elementos sin comprometer la integridad de la base de datos.

---

## 🧱 Tecnologías utilizadas

- Symfony 6
- Doctrine ORM
- Twig
- MySQL
- Bootstrap 5
- API de TMDB
- JavaScript (para interacciones como rankings drag & drop)

---

## 🗄️ Modelo de datos

El sistema se basa principalmente en estas entidades:

- User
- Movie
- Review
- Category
- CategoryRanking

Las relaciones entre ellas permiten mantener valoraciones individuales, rankings personalizados y estadísticas agregadas sin perder consistencia.

---

## ⚙️ Instalación

Clonar el repositorio:

```bash
git clone https://github.com/tu-usuario/theMovieReviewer.git
cd theMovieReviewer
