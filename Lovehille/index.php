<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

$user = require_login();
$profileInitial = mb_strtoupper(mb_substr($user['name'], 0, 1, 'UTF-8'), 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#688E5A">
  <meta name="description" content="LoveFilm — a little picture story, made for movie night.">
  <meta name="csrf-token" content="<?= escape_html(csrf_token()) ?>">
  <title>LoveFilm — Watch something wonderful</title>
  <style>
    :root {
      color-scheme: dark;
      font-family: Arial, Helvetica, sans-serif;
      background: #101410;
      color: #f5f5f1;
      font-synthesis: none;
      text-rendering: optimizeLegibility;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      --olive: #688E5A;
      --olive-deep: #4F6C46;
      --muted: #aaa;
    }

    *, *::before, *::after { box-sizing: border-box; }
    html { min-width: 320px; scroll-behavior: smooth; }
    body { margin: 0; min-height: 100vh; }
    button, input { font: inherit; }
    button { color: inherit; }
    button:focus-visible, a:focus-visible, input:focus-visible {
      outline: 2px solid #fff;
      outline-offset: 4px;
    }

    .header {
      position: absolute;
      z-index: 2;
      top: 0;
      left: 0;
      display: flex;
      width: 100%;
      min-height: 74px;
      align-items: center;
      gap: 38px;
      padding: 12px 5.1%;
      background: linear-gradient(180deg, rgb(0 0 0 / 66%), transparent);
    }

    .brand {
      color: var(--olive);
      font-size: clamp(1.45rem, 3vw, 2.05rem);
      font-weight: 900;
      letter-spacing: -.07em;
      line-height: 1;
      text-decoration: none;
      white-space: nowrap;
    }

    .nav { display: flex; align-items: center; gap: 23px; }
    .nav a {
      color: #e5e5e5;
      font-size: .85rem;
      text-decoration: none;
      transition: color .2s;
    }
    .nav a:hover, .nav a[aria-current="page"] { color: #aaa; }
    .header-actions { display: flex; align-items: center; gap: 19px; margin-left: auto; }
    .search { position: relative; display: flex; align-items: center; }
    .search input {
      width: 0;
      padding: 8px 0;
      border: 1px solid transparent;
      border-radius: 2px;
      background: transparent;
      color: white;
      opacity: 0;
      transition: width .2s, opacity .2s, padding .2s;
    }
    .search:focus-within input, .search.is-open input {
      width: min(34vw, 225px);
      padding: 8px 10px;
      border-color: #ddd;
      background: rgb(0 0 0 / 72%);
      opacity: 1;
    }
    .icon-button {
      display: inline-grid;
      width: 38px;
      height: 38px;
      flex: 0 0 auto;
      place-items: center;
      padding: 0;
      border: 0;
      border-radius: 50%;
      background: transparent;
      cursor: pointer;
    }
    .icon-button:hover { background: rgb(255 255 255 / 14%); }
    .icon-button svg { width: 20px; height: 20px; }
    .avatar {
      display: grid;
      width: 32px;
      height: 32px;
      flex: 0 0 auto;
      place-items: center;
      border-radius: 5px;
      background: linear-gradient(145deg, #e8ae7e, #78362f);
      font-size: .8rem;
      font-weight: 800;
    }
    .account-name {
      max-width: 125px;
      overflow: hidden;
      color: #eee;
      font-size: .81rem;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .logout-form { margin: 0; }
    .logout-button {
      padding: 8px 12px;
      border: 1px solid rgb(255 255 255 / 28%);
      border-radius: 3px;
      background: rgb(20 20 20 / 48%);
      color: #eee;
      font-size: .76rem;
      cursor: pointer;
      transition: background .2s, border-color .2s;
    }
    .logout-button:hover { border-color: rgb(255 255 255 / 62%); background: rgb(255 255 255 / 11%); }

    .hero {
      position: relative;
      display: flex;
      min-height: min(760px, 85svh);
      align-items: center;
      overflow: hidden;
      background-color: #1d2616;
      background-image:
        linear-gradient(90deg, rgb(12 12 12 / 90%) 0%, rgb(12 12 12 / 66%) 36%, rgb(12 12 12 / 13%) 76%),
        linear-gradient(0deg, #101010 0%, transparent 35%, rgb(0 0 0 / 20%) 100%),
        url("Picture/pic3.webp");
      background-position: center, center, 72% 43%;
      background-size: cover;
    }

    .hero::after {
      position: absolute;
      right: 0;
      bottom: 0;
      left: 0;
      height: 20%;
      background: linear-gradient(0deg, #101010, transparent);
      content: "";
      pointer-events: none;
    }

    .hero-content {
      position: relative;
      z-index: 1;
      width: min(570px, 90%);
      margin: 93px 5.1% 60px;
    }
    .eyebrow {
      display: flex;
      align-items: center;
      gap: 9px;
      margin-bottom: 12px;
      color: #eee;
      font-size: .77rem;
      font-weight: 700;
      letter-spacing: .18em;
      text-transform: uppercase;
    }
    .eyebrow-mark {
      display: inline-grid;
      width: 24px;
      height: 25px;
      place-items: center;
      background: var(--olive);
      font-size: .92rem;
      letter-spacing: -.06em;
    }
    .hero h1 {
      margin: 0 0 17px;
      font-size: clamp(3.6rem, 9vw, 6.8rem);
      font-weight: 900;
      letter-spacing: -.075em;
      line-height: .96;
      text-shadow: 0 3px 24px rgb(0 0 0 / 35%);
    }
    .details { display: flex; align-items: center; gap: 12px; color: #ddd; font-size: .86rem; }
    .match { color: #46d369; font-weight: 700; }
    .rating { padding: 2px 6px; border: 1px solid #aaa; color: #eee; font-size: .75rem; }
    .hero-copy { max-width: 480px; margin: 16px 0 22px; color: #eee; font-size: 1rem; line-height: 1.65; text-shadow: 0 1px 4px #000; }
    .buttons { display: flex; flex-wrap: wrap; gap: 10px; }
    .button {
      display: inline-flex;
      min-height: 44px;
      align-items: center;
      justify-content: center;
      gap: 9px;
      padding: 10px 19px;
      border: 0;
      border-radius: 4px;
      font-weight: 700;
      cursor: pointer;
      transition: filter .18s, background .18s, transform .18s;
    }
    .button:hover { filter: brightness(.84); }
    .button:active { transform: scale(.98); }
    .button-light { background: #f5f5f1; color: #171717; }
    .button-dark { background: rgb(104 142 90 / 80%); color: #fff; }
    .button-dark:hover { background: rgb(109 109 110 / 45%); }
    .button svg { width: 20px; height: 20px; }

    main { position: relative; z-index: 1; margin-top: -12px; padding-bottom: 44px; }
    .row { padding: 8px 5.1% 28px; }
    .row-heading {
      display: flex;
      min-height: 39px;
      align-items: center;
      gap: 9px;
      margin: 0 0 14px;
      font-size: clamp(1.1rem, 2vw, 1.45rem);
      font-weight: 700;
      letter-spacing: -.025em;
    }
    .row-heading .arrow { color: #54b9c5; font-size: .85rem; opacity: 0; transition: opacity .2s; }
    .row-heading:hover .arrow { opacity: 1; }
    .cards {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 16px;
    }
    .card {
      position: relative;
      overflow: hidden;
      min-width: 0;
      aspect-ratio: 16 / 9;
      border-radius: 5px;
      background: #20170f;
      cursor: pointer;
      isolation: isolate;
      transition: transform .22s, box-shadow .22s;
    }
    .card-open { position: absolute; z-index: 1; inset: 0; width: 100%; padding: 0; border: 0; background: transparent; color: inherit; text-align: left; cursor: pointer; }
    .card-open:focus-visible { outline-offset: -4px; }
    .card:hover { z-index: 1; transform: scale(1.045); box-shadow: 0 8px 28px rgb(0 0 0 / 55%); }
    .card img {
      position: absolute;
      width: 100%;
      height: 100%;
      inset: 0;
      object-fit: cover;
      object-position: center 43%;
      transition: filter .25s, transform .35s;
    }
    .card:hover img { filter: brightness(.55); transform: scale(1.04); }
    .card-gradient { position: absolute; inset: 30% 0 0; background: linear-gradient(transparent, rgb(0 0 0 / 78%)); }
    .card-number {
      position: absolute;
      top: 10px;
      left: 12px;
      color: rgb(255 255 255 / 86%);
      font-size: .67rem;
      font-weight: 700;
      letter-spacing: .17em;
    }
    .card-play {
      position: absolute;
      top: 50%;
      left: 50%;
      display: grid;
      width: 48px;
      height: 48px;
      place-items: center;
      transform: translate(-50%, -42%) scale(.8);
      border: 1px solid rgb(255 255 255 / 75%);
      border-radius: 50%;
      background: rgb(0 0 0 / 35%);
      opacity: 0;
      transition: opacity .2s, transform .2s;
    }
    .card:hover .card-play { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    .card-play svg { width: 20px; height: 20px; }
    .card-caption { position: absolute; right: 14px; bottom: 13px; left: 14px; }
    .card-caption strong { display: block; font-size: clamp(.95rem, 2.2vw, 1.22rem); text-shadow: 0 2px 8px #000; }
    .card-caption span { display: block; margin-top: 4px; color: #ddd; font-size: .77rem; text-shadow: 0 2px 8px #000; }
    .empty-state { padding: 28px 0; color: var(--muted); }
    .favorite-status {
      margin: 9px 0 0;
      color: #ffc7bd;
      font-size: .84rem;
      line-height: 1.5;
    }
    .card-save:disabled { cursor: wait; opacity: .65; }

    footer {
      padding: 30px 5.1% 40px;
      border-top: 1px solid rgb(255 255 255 / 9%);
      color: #888;
      font-size: .78rem;
      line-height: 1.7;
    }
    .footer-brand { margin-bottom: 6px; color: #ccc; font-size: 1rem; font-weight: 800; }

    .player {
      position: fixed;
      z-index: 5;
      inset: 0;
      display: grid;
      place-items: center;
      padding: 30px 5%;
      background: rgb(0 0 0 / 88%);
      opacity: 0;
      transition: opacity .22s;
    }
    .player[hidden] { display: none; }
    .player.is-visible { opacity: 1; }
    .player-panel { width: min(100%, 1020px); }
    .player-top { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 12px; }
    .player-title { font-size: 1rem; font-weight: 700; }
    .player-screen { position: relative; overflow: hidden; aspect-ratio: 16 / 9; background: #090806; box-shadow: 0 20px 80px #000; }
    .player-screen img { display: block; width: 100%; height: 100%; object-fit: contain; }
    .player-shade {
      position: absolute;
      inset: 40% 0 0;
      display: flex;
      align-items: flex-end;
      padding: 24px;
      background: linear-gradient(transparent, rgb(0 0 0 / 74%));
      pointer-events: none;
    }
    .player-caption { margin: 0; font-size: clamp(1.1rem, 4vw, 2.6rem); font-weight: 800; letter-spacing: .04em; text-shadow: 0 2px 12px #000; }
    .player-controls { display: flex; align-items: center; gap: 11px; margin-top: 12px; }
    .player-controls .icon-button { border: 1px solid rgb(255 255 255 / 28%); }
    .player-controls .icon-button svg { width: 17px; height: 17px; }
    .progress { height: 3px; flex: 1; overflow: hidden; border-radius: 3px; background: #555; }
    .progress-fill { width: 0; height: 100%; background: var(--olive); transition: width .2s linear; }
    .player-time { min-width: 90px; color: #ddd; font-size: .76rem; text-align: right; }

    @media (min-width: 1500px) {
      .header, .hero-content, .row, footer { padding-right: max(5.1%, calc((100vw - 1450px) / 2)); padding-left: max(5.1%, calc((100vw - 1450px) / 2)); }
      .hero-content { margin-right: 0; margin-left: 0; }
    }
    @media (max-width: 700px) {
      .header { min-height: 64px; gap: 18px; padding: 12px 4%; }
      .nav { gap: 14px; }
      .nav a { font-size: .77rem; }
      .header-actions { gap: 8px; }
      .account-name { max-width: 83px; }
      .hero { min-height: min(680px, 84svh); background-position: center, center, 62% center; }
      .hero-content { width: min(490px, 92%); margin: 92px 4% 50px; }
      .hero-copy { max-width: 410px; font-size: .93rem; }
      .row { padding-right: 4%; padding-left: 4%; }
      .cards { gap: 10px; }
      .card-caption { right: 9px; bottom: 9px; left: 9px; }
      .card-caption span { font-size: .68rem; }
      footer { padding-right: 4%; padding-left: 4%; }
    }
    @media (max-width: 460px) {
      .header { gap: 12px; }
      .nav { gap: 12px; }
      .nav a { font-size: .71rem; }
      .header-actions { gap: 1px; }
      .header-actions > .icon-button { width: 32px; }
      .account-name { display: none; }
      .logout-button { padding: 7px 8px; font-size: .68rem; }
      .avatar { width: 28px; height: 28px; }
      .hero { min-height: 620px; min-height: 78svh; background-position: center, center, 59% center; }
      .hero-content { margin-top: 82px; }
      .cards { grid-template-columns: 1fr; gap: 13px; }
      .card { aspect-ratio: 16 / 8; }
      .card-caption strong { font-size: 1.1rem; }
      .card-caption span { font-size: .77rem; }
    }
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; animation-duration: .01ms !important; animation-iteration-count: 1 !important; }
    }
  </style>
</head>
<body>
  <header class="header">
    <a class="brand" href="index.php" aria-label="LoveFilm home">LOVEFILM</a>
    <nav class="nav" aria-label="Main navigation">
      <a href="#" aria-current="page">Home</a>
      <a href="#films">Films</a>
      <a href="#my-list">My List</a>
    </nav>
    <div class="header-actions">
      <form class="search" id="search-form" role="search">
        <input id="search-input" type="search" placeholder="Search titles" aria-label="Search titles">
        <button class="icon-button" id="search-toggle" type="button" aria-label="Open search" aria-expanded="false">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 4.5 4.5"></path></svg>
        </button>
      </form>
      <button class="icon-button" id="list-link" type="button" aria-label="Show my list" title="My List">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 4.5h14a1 1 0 0 1 1 1v15l-8-4-8 4v-15a1 1 0 0 1 1-1Z"></path><path d="M12 7v7m-3.5-3.5h7"></path></svg>
      </button>
      <span class="account-name" title="<?= escape_html($user['name']) ?>"><?= escape_html($user['name']) ?></span>
      <span class="avatar" aria-label="<?= escape_html($user['name']) ?>’s profile"><?= escape_html($profileInitial) ?></span>
      <form class="logout-form" method="post" action="logout.php">
        <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
        <button class="logout-button" type="submit">Sign out</button>
      </form>
    </div>
  </header>

  <section class="hero" aria-labelledby="hero-title">
    <div class="hero-content">
      <div class="eyebrow"><span class="eyebrow-mark" aria-hidden="true">L</span> A LoveFilm original</div>
      <h1 id="hero-title">LoveFilm</h1>
      <div class="details"><span class="match">98% Match</span><span>2026</span><span class="rating">ALL</span><span>Picture story</span></div>
      <p class="hero-copy">Three little moments. One vintage picture story. Step into “Off Campus,” pause at “Sterling Point,” and stay for “Last Sunrise.”</p>
      <div class="buttons">
        <button class="button button-light" id="hero-play" type="button">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 4.8a1 1 0 0 1 1.52-.85l11.02 6.7a1.58 1.58 0 0 1 0 2.7l-11.02 6.7A1 1 0 0 1 7 19.2Z"></path></svg>
          Play
        </button>
        <button class="button button-dark" id="details-button" type="button">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5m0-8h.01"></path></svg>
          More Info
        </button>
      </div>
    </div>
  </section>

  <main>
    <section class="row" id="films" aria-labelledby="films-heading">
      <h2 class="row-heading" id="films-heading">The LoveFilm Collection <span class="arrow" aria-hidden="true">Explore all ›</span></h2>
      <div class="cards" id="movie-cards" aria-live="polite">
        <article class="card">
          <img src="Picture/pic1.jpg" alt="" aria-hidden="true" loading="lazy">
          <div class="card-gradient" aria-hidden="true"></div>
          <button class="card-open" type="button" aria-label="Play Off Campus">
            <span class="card-number" aria-hidden="true">LOVEFILM 01</span>
            <span class="card-play" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.7a1 1 0 0 1 1.52-.85l10 6.1a1.23 1.23 0 0 1 0 2.1l-10 6.1A1 1 0 0 1 8 18.3Z"></path></svg></span>
            <span class="card-caption" aria-hidden="true"><strong>Off Campus</strong><span>A little beginning.</span></span>
          </button>
          <button class="icon-button card-save" type="button" style="position:absolute;top:7px;right:8px;z-index:2;background:rgb(0 0 0 / 55%);border:1px solid rgb(255 255 255 / 55%)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 4.5h14a1 1 0 0 1 1 1v15l-8-4-8 4v-15a1 1 0 0 1 1-1Z"></path><path d="M12 8v6m-3-3h6"></path></svg>
          </button>
        </article>
        <article class="card">
          <img src="Picture/pic2.jpg" alt="" aria-hidden="true" loading="lazy">
          <div class="card-gradient" aria-hidden="true"></div>
          <button class="card-open" type="button" aria-label="Play Sterling Point">
            <span class="card-number" aria-hidden="true">LOVEFILM 02</span>
            <span class="card-play" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.7a1 1 0 0 1 1.52-.85l10 6.1a1.23 1.23 0 0 1 0 2.1l-10 6.1A1 1 0 0 1 8 18.3Z"></path></svg></span>
            <span class="card-caption" aria-hidden="true"><strong>Sterling Point</strong><span>A moment to remember.</span></span>
          </button>
          <button class="icon-button card-save" type="button" style="position:absolute;top:7px;right:8px;z-index:2;background:rgb(0 0 0 / 55%);border:1px solid rgb(255 255 255 / 55%)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 4.5h14a1 1 0 0 1 1 1v15l-8-4-8 4v-15a1 1 0 0 1 1-1Z"></path><path d="M12 8v6m-3-3h6"></path></svg>
          </button>
        </article>
        <article class="card">
          <img src="Picture/pic3.webp" alt="" aria-hidden="true" loading="lazy">
          <div class="card-gradient" aria-hidden="true"></div>
          <button class="card-open" type="button" aria-label="Play Last Sunrise">
            <span class="card-number" aria-hidden="true">LOVEFILM 03</span>
            <span class="card-play" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.7a1 1 0 0 1 1.52-.85l10 6.1a1.23 1 0 0 1 0 2.1l-10 6.1A1 1 0 0 1 8 18.3Z"></path></svg></span>
            <span class="card-caption" aria-hidden="true"><strong>Last Sunrise</strong><span>Every ending, a new light.</span></span>
          </button>
          <button class="icon-button card-save" type="button" style="position:absolute;top:7px;right:8px;z-index:2;background:rgb(0 0 0 / 55%);border:1px solid rgb(255 255 255 / 55%)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 4.5h14a1 1 0 0 1 1 1v15l-8-4-8 4v-15a1 1 0 0 1 1-1Z"></path><path d="M12 8v6m-3-3h6"></path></svg>
          </button>
        </article>
      </div>
      <p class="empty-state" id="empty-state" hidden>No titles found. Try another search.</p>
      <p class="favorite-status" id="favorite-status" role="status" aria-live="polite" hidden></p>
    </section>
    <section class="row" id="my-list" aria-label="Your saved titles" hidden>
      <h2 class="row-heading">My List</h2>
      <div class="cards" id="saved-cards"></div>
    </section>
  </main>

  <footer>
    <div class="footer-brand">LOVEFILM</div>
    <div>A little picture story, made for movie night.</div>
  </footer>

  <section class="player" id="player" role="dialog" aria-modal="true" aria-labelledby="player-title" aria-hidden="true" hidden>
    <div class="player-panel">
      <div class="player-top">
        <div class="player-title" id="player-title">LoveFilm</div>
        <button class="icon-button" id="close-player" type="button" aria-label="Close movie">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"></path></svg>
        </button>
      </div>
      <div class="player-screen">
        <img id="player-image" src="Picture/pic1.jpg" alt="">
        <div class="player-shade"><p class="player-caption" id="player-caption">OFF CAMPUS</p></div>
      </div>
      <div class="player-controls">
        <button class="icon-button" id="toggle-play" type="button" aria-label="Pause movie"></button>
        <button class="icon-button" id="previous-scene" type="button" aria-label="Previous scene">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 5h2v14H5zM20 6.8v10.4a1 1 0 0 1-1.5.86l-8-5.2a1 1 0 0 1 0-1.72l8-5.2a1 1 0 0 1 1.5.86Z"></path></svg>
        </button>
        <button class="icon-button" id="next-scene" type="button" aria-label="Next scene">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17 5h2v14h-2zM4 6.8v10.4a1 1 0 0 0 1.5.86l8-5.2a1 1 0 0 0 0-1.72l-8-5.2A1 1 0 0 0 4 6.8Z"></path></svg>
        </button>
        <div class="progress" role="progressbar" aria-label="Movie progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-fill" id="progress-fill"></div></div>
        <span class="player-time" id="player-time">00:00 / 00:15</span>
      </div>
    </div>
  </section>

  <script>
    (() => {
      const scenes = [
        { title: "Off Campus", src: "Picture/pic1.jpg" },
        { title: "Sterling Point", src: "Picture/pic2.jpg" },
        { title: "Last Sunrise", src: "Picture/pic3.webp" }
      ];
      const player = document.getElementById("player");
      const playerImage = document.getElementById("player-image");
      const playerCaption = document.getElementById("player-caption");
      const playerTitle = document.getElementById("player-title");
      const progress = document.getElementById("progress-fill");
      const progressBar = document.querySelector(".progress");
      const timeDisplay = document.getElementById("player-time");
      const playButton = document.getElementById("toggle-play");
      const searchForm = document.getElementById("search-form");
      const searchInput = document.getElementById("search-input");
      const searchToggle = document.getElementById("search-toggle");
      const cardsContainer = document.getElementById("movie-cards");
      const emptyState = document.getElementById("empty-state");
      const myListSection = document.getElementById("my-list");
      const savedCards = document.getElementById("saved-cards");
      const favoriteStatus = document.getElementById("favorite-status");
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
      const favoritesEndpoint = "favorites.php";
      const movieSlugs = ["off-campus", "sterling-point", "last-sunrise"];
      const sceneDuration = 5;
      let activeScene = 0;
      let elapsed = 0;
      let playing = false;
      let favoritesReady = false;
      let timer = null;
      let closeTimer = null;
      let previousFocus = null;

      function favoriteRequestError(response, result) {
        if (result && typeof result.error === "string") return new Error(result.error);
        if (response.status === 401) return new Error("Please sign in again to access your saved titles.");
        return new Error("Your saved titles could not be updated. Please try again.");
      }

      async function syncSavedTitles() {
        try {
          const response = await fetch(favoritesEndpoint, {
            credentials: "same-origin",
            cache: "no-store",
            headers: { "Accept": "application/json" }
          });
          const result = await response.json();
          if (!response.ok) throw favoriteRequestError(response, result);
          if (!Array.isArray(result.favorites)) throw new Error("The saved titles response was invalid.");
          savedTitles = result.favorites.filter(slug => movieSlugs.includes(slug));
          favoritesReady = true;
          renderSavedCards();
          showFavoriteMessage("");
        } catch (error) {
          console.error("Could not load your saved LoveFilm titles.", error);
          showFavoriteMessage(error instanceof Error ? error.message : "Your saved titles could not be loaded. Refresh the page to try again.");
        }
      }

      let savedTitles = [];

      function showFavoriteMessage(message) {
        favoriteStatus.textContent = message;
        favoriteStatus.hidden = message === "";
      }

      function updatePlayButton() {
        playButton.setAttribute("aria-label", playing ? "Pause movie" : "Play movie");
        playButton.innerHTML = playing
          ? '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6 5h4v14H6zm8 0h4v14h-4z"></path></svg>'
          : '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 4.8a1 1 0 0 1 1.52-.85l11.02 6.7a1.58 1.58 0 0 1 0 2.7l-11.02 6.7A1 1 0 0 1 7 19.2Z"></path></svg>';
      }

      function updateScene() {
        const scene = scenes[activeScene];
        playerImage.src = scene.src;
        playerImage.alt = scene.title;
        playerCaption.textContent = scene.title.toUpperCase();
        playerTitle.textContent = `LoveFilm · ${scene.title}`;
      }

      function updateProgress() {
        const total = scenes.length * sceneDuration;
        const current = activeScene * sceneDuration + elapsed;
        const percentage = current / total * 100;
        progress.style.width = `${percentage}%`;
        progressBar.setAttribute("aria-valuenow", String(Math.round(percentage)));
        const format = value => `00:${String(Math.floor(value)).padStart(2, "0")}`;
        timeDisplay.textContent = `${format(current)} / ${format(total)}`;
      }

      function stopPlayback() {
        playing = false;
        window.clearInterval(timer);
        timer = null;
        updatePlayButton();
      }

      function startPlayback() {
        if (playing) return;
        if (activeScene === scenes.length - 1 && elapsed >= sceneDuration) {
          activeScene = 0;
          elapsed = 0;
          updateScene();
          updateProgress();
        }
        playing = true;
        updatePlayButton();
        timer = window.setInterval(() => {
          elapsed += 1;
          if (elapsed >= sceneDuration) {
            elapsed = 0;
            if (activeScene < scenes.length - 1) {
              activeScene += 1;
              updateScene();
            } else {
              stopPlayback();
            }
          }
          updateProgress();
        }, 1000);
      }

      function showScene(index) {
        activeScene = (index + scenes.length) % scenes.length;
        elapsed = 0;
        updateScene();
        updateProgress();
        if (!playing && index === 0 && player.dataset.autoplay === "true") {
          player.dataset.autoplay = "false";
          startPlayback();
        }
      }

      function openPlayer(sceneIndex = 0) {
        window.clearTimeout(closeTimer);
        closeTimer = null;
        previousFocus = document.activeElement;
        stopPlayback();
        player.dataset.autoplay = "true";
        showScene(sceneIndex);
        player.hidden = false;
        player.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
        requestAnimationFrame(() => {
          player.classList.add("is-visible");
          document.getElementById("close-player").focus();
        });
      }

      function closePlayer() {
        stopPlayback();
        player.classList.remove("is-visible");
        player.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
        window.clearTimeout(closeTimer);
        closeTimer = window.setTimeout(() => {
          player.hidden = true;
          closeTimer = null;
        }, 230);
        if (previousFocus instanceof HTMLElement) previousFocus.focus();
      }

      function renderSavedCards() {
        savedCards.replaceChildren();
        myListSection.hidden = savedTitles.length === 0;
        savedTitles.forEach(slug => {
          const index = movieSlugs.indexOf(slug);
          if (index < 0) return;
          const card = cardsContainer.children[index].cloneNode(true);
          card.dataset.sceneIndex = String(index);
          savedCards.append(card);
        });
        refreshSaveButtons();
      }

      function refreshSaveButtons() {
        document.querySelectorAll(".card").forEach(card => {
          const index = Number(card.dataset.sceneIndex);
          const scene = scenes[index];
          if (!scene) return;
          const saveButton = card.querySelector(".card-save");
          const isSaved = savedTitles.includes(movieSlugs[index]);
          saveButton.disabled = !favoritesReady;
          saveButton.setAttribute("aria-label", `${isSaved ? "Remove" : "Add"} ${scene.title} ${isSaved ? "from" : "to"} My List`);
          saveButton.title = isSaved ? "Remove from My List" : "Add to My List";
          saveButton.innerHTML = isSaved
            ? '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 4.5h14a1 1 0 0 1 1 1v15l-8-4-8 4v-15a1 1 0 0 1 1-1Z"></path></svg>'
            : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 4.5h14a1 1 0 0 1 1 1v15l-8-4-8 4v-15a1 1 0 0 1 1-1Z"></path><path d="M12 8v6m-3-3h6"></path></svg>';
        });
      }

      function playCard(card) {
        const sceneIndex = Number(card.closest(".card").dataset.sceneIndex);
        openPlayer(Number.isInteger(sceneIndex) && sceneIndex >= 0 && sceneIndex < scenes.length ? sceneIndex : 0);
      }

      [...cardsContainer.children].forEach((card, index) => {
        card.dataset.sceneIndex = String(index);
        card.dataset.movieSlug = movieSlugs[index];
      });

      for (const collection of [cardsContainer, savedCards]) {
        collection.addEventListener("click", async event => {
          const card = event.target.closest(".card");
          if (!card || !collection.contains(card)) return;
          const sceneIndex = Number(card.dataset.sceneIndex);
          const movie = movieSlugs[sceneIndex];
          if (!scenes[sceneIndex] || !movie) return;
          if (event.target.closest(".card-open")) {
            playCard(card);
            return;
          }
          const saveButton = event.target.closest(".card-save");
          if (!saveButton || !favoritesReady || saveButton.disabled) return;
          const wasSaved = savedTitles.includes(movie);
          saveButton.disabled = true;
          try {
            const response = await fetch(favoritesEndpoint, {
              method: wasSaved ? "DELETE" : "POST",
              credentials: "same-origin",
              headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-Token": csrfToken
              },
              body: JSON.stringify({ movie })
            });
            const result = await response.json();
            if (!response.ok) throw favoriteRequestError(response, result);
            savedTitles = wasSaved
              ? savedTitles.filter(slug => slug !== movie)
              : [...savedTitles, movie];
            renderSavedCards();
            showFavoriteMessage("");
          } catch (error) {
            console.error("Could not update your LoveFilm watchlist.", error);
            showFavoriteMessage(error instanceof Error ? error.message : "Your saved titles could not be updated. Please try again.");
            saveButton.disabled = false;
          }
        });
      }

      document.getElementById("hero-play").addEventListener("click", () => openPlayer(0));
      document.getElementById("details-button").addEventListener("click", () => {
        document.getElementById("films").scrollIntoView({ behavior: "smooth" });
      });
      document.getElementById("list-link").addEventListener("click", () => {
        myListSection.hidden = savedTitles.length === 0;
        document.getElementById("my-list").scrollIntoView({ behavior: "smooth" });
      });
      document.getElementById("close-player").addEventListener("click", closePlayer);
      document.getElementById("previous-scene").addEventListener("click", () => showScene(activeScene - 1));
      document.getElementById("next-scene").addEventListener("click", () => showScene(activeScene + 1));
      playButton.addEventListener("click", () => playing ? stopPlayback() : startPlayback());
      player.addEventListener("click", event => { if (event.target === player) closePlayer(); });
      searchToggle.addEventListener("click", () => {
        const expanded = searchForm.classList.toggle("is-open");
        searchToggle.setAttribute("aria-expanded", String(expanded));
        searchToggle.setAttribute("aria-label", expanded ? "Close search" : "Open search");
        if (expanded) searchInput.focus();
        else { searchInput.value = ""; filterCards(""); }
      });
      searchForm.addEventListener("submit", event => event.preventDefault());
      searchInput.addEventListener("input", () => filterCards(searchInput.value.trim().toLowerCase()));

      function filterCards(query) {
        let visible = 0;
        [...cardsContainer.children].forEach((card, index) => {
          const matches = `${scenes[index].title} ${card.querySelector(".card-caption span").textContent}`.toLowerCase().includes(query);
          card.hidden = !matches;
          if (matches) visible += 1;
        });
        emptyState.hidden = visible !== 0;
      }

      document.addEventListener("keydown", event => {
        if (player.hidden) return;
        if (event.key === "Escape") closePlayer();
        if (event.key === "ArrowRight") showScene(activeScene + 1);
        if (event.key === "ArrowLeft") showScene(activeScene - 1);
        if (event.key === " " && !(event.target instanceof HTMLButtonElement)) {
          event.preventDefault();
          playing ? stopPlayback() : startPlayback();
        }
      });

      renderSavedCards();
      refreshSaveButtons();
      showScene(0);
      updatePlayButton();
      void syncSavedTitles();
    })();
  </script>
</body>
</html>
