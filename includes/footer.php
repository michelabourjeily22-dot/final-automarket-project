<?php

$lastUpdated = '2025-12-5'; 

?>

<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-section">
                <h3><?php echo htmlspecialchars(SITE_NAME); ?></h3>
                <p>Your trusted marketplace for buying and selling vehicles.</p>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Browse Listings</a></li>
                    <li><a href="search.php">Search Auto</a></li>
                    <li><a href="add_post.php">Post Auto</a></li>
                    <li><a href="research.php">Research</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#disclaimer">Disclaimer</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-disclaimer" id="disclaimer">
            <h4>Disclaimer</h4>
            <p>
                This website is provided for informational purposes only. All vehicle listings are posted by third parties, 
                and <?php echo htmlspecialchars(SITE_NAME); ?> does not verify the accuracy of listings, vehicle condition, 
                or seller information. Buyers are advised to inspect vehicles personally and verify all information before 
                purchase. <?php echo htmlspecialchars(SITE_NAME); ?> is not responsible for any transactions, disputes, or 
                issues arising from listings on this platform. All sales are between buyers and sellers directly. 
                Prices and availability are subject to change without notice.
            </p>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?>. All rights reserved.</p>
            <p class="last-updated">Last updated: <?php echo htmlspecialchars($lastUpdated); ?></p>
        </div>
    </div>
</footer>

<style>
.site-footer {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-top: 1px solid var(--border-color);
    margin-top: 60px;
    padding: 40px 24px 20px;
}

.footer-container {
    max-width: 1400px;
    margin: 0 auto;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 32px;
    margin-bottom: 32px;
}

.footer-section h3,
.footer-section h4 {
    color: var(--text-primary);
    margin-bottom: 16px;
    font-size: 18px;
    font-weight: 700;
}

.footer-section h4 {
    font-size: 16px;
}

.footer-section p {
    color: var(--text-secondary);
    line-height: 1.6;
    font-size: 14px;
}

.footer-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-section ul li {
    margin-bottom: 8px;
}

.footer-section ul li a {
    color: var(--text-secondary);
    text-decoration: none;
    transition: color var(--transition-base);
    font-size: 14px;
}

.footer-section ul li a:hover {
    color: var(--primary-color);
}

.footer-disclaimer {
    background: rgba(26, 26, 26, 0.6);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
}

.footer-disclaimer h4 {
    color: var(--text-primary);
    margin-bottom: 12px;
    font-size: 16px;
    font-weight: 700;
}

.footer-disclaimer p {
    color: var(--text-secondary);
    font-size: 13px;
    line-height: 1.6;
    margin: 0;
}

.footer-bottom {
    text-align: center;
    padding-top: 24px;
    border-top: 1px solid var(--border-color);
}

.footer-bottom p {
    color: var(--text-secondary);
    font-size: 13px;
    margin: 4px 0;
}

.footer-bottom .last-updated {
    color: var(--text-tertiary);
    font-size: 12px;
}

@media (max-width: 767px) {
    .site-footer {
        padding: 32px 16px 16px;
    }

    .footer-content {
        grid-template-columns: 1fr;
        gap: 24px;
    }

    .footer-disclaimer {
        padding: 20px;
    }
}
</style>

