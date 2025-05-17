<footer class="footer">
    <div class="footer__container">
        <div class="footer__logo">
            <img src="/assets/img/svg/logo.svg" alt="MovieRate" height="32">
            <span>MovieRate</span>
        </div>
        <div class="footer__info">
            <span>&copy; <?= date('Y') ?> MovieRate. Все права защищены.</span>
            <span>Проект для учебных целей</span>
        </div>
        <div class="footer__links">
            <a href="/" class="footer__link">Главная</a>
            <a href="/about" class="footer__link">О проекте</a>
            <a href="https://github.com/" target="_blank" class="footer__link">GitHub</a>
        </div>
    </div>
</footer>
<script src="/assets/js/app.js"></script>
</body>

</html>
<style>
    .footer {
        background: #222c3a;
        color: #fff;
        padding: 28px 0 18px 0;
        margin-top: 40px;
        font-size: 1rem;
    }

    .footer__container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 0 18px;
    }

    .footer__logo {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.18rem;
        font-weight: 600;
        color: #4f8cff;
    }

    .footer__info {
        display: flex;
        flex-direction: column;
        gap: 2px;
        font-size: 0.98rem;
        color: #b0b0b0;
    }

    .footer__links {
        display: flex;
        gap: 18px;
    }

    .footer__link {
        color: #fff;
        text-decoration: none;
        font-size: 1rem;
        transition: color 0.2s;
    }

    .footer__link:hover {
        color: #4f8cff;
        text-decoration: underline;
    }

    @media (max-width: 700px) {
        .footer__container {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
    }
</style>