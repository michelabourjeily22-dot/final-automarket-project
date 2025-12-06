<?php
/**
 * Research Page
 * 
 * Contains research references, citations, and AI-use disclosure
 */

$lastUpdated = date('Y-m-d');
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Research and references for the Auto Marketplace project, including technology choices, design decisions, and academic citations.">
    <title>Research - <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/buttons.css">
    <style>
        .research-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 24px;
        }
        
        .research-header {
            margin-bottom: 40px;
        }
        
        .research-header h1 {
            font-size: 36px;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
        }
        
        .last-updated {
            color: var(--text-tertiary);
            font-size: 14px;
            margin-bottom: 32px;
        }
        
        .research-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-sm);
        }
        
        .research-section h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 12px;
        }
        
        .research-section h3 {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin-top: 24px;
            margin-bottom: 12px;
        }
        
        .research-section p {
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: 16px;
        }
        
        .research-section ul,
        .research-section ol {
            color: var(--text-secondary);
            line-height: 1.8;
            margin-left: 24px;
            margin-bottom: 16px;
        }
        
        .research-section li {
            margin-bottom: 8px;
        }
        
        .citation {
            background: rgba(26, 26, 26, 0.6);
            border-left: 3px solid var(--primary-color);
            padding: 16px;
            margin: 16px 0;
            border-radius: 8px;
        }
        
        .citation p {
            margin: 0;
            font-size: 14px;
            font-style: italic;
            color: var(--text-secondary);
        }
        
        .ai-disclosure {
            background: rgba(138, 43, 226, 0.1);
            border: 1px solid var(--primary-color);
            border-radius: 12px;
            padding: 24px;
            margin-top: 32px;
        }
        
        .ai-disclosure h3 {
            color: var(--primary-color);
            margin-top: 0;
        }
        
        @media (max-width: 767px) {
            .research-container {
                padding: 24px 16px;
            }
            
            .research-section {
                padding: 24px 20px;
            }
            
            .research-header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="research-container">
        <header class="research-header">
            <h1>Research & References</h1>
            <p class="last-updated">Last updated: <?php echo htmlspecialchars($lastUpdated); ?></p>
        </header>
        
        <section class="research-section">
            <h2>Research & References</h2>
            
            <p>
                This project was developed after reviewing documentation and tutorials related to web development, databases, and authentication. 
                Key resources include:
            </p>
            
            <h3>Research</h3>
            
            <p>
                I researched and learned various technologies and concepts to build this project. This included mastering PHP and MySQL for backend functionalities such as user login and registration, session management, and full CRUD (Create, Read, Update, Delete) operations for car listings. A significant part of this involved handling secure image uploads and managing database interactions efficiently.
            </p>
            
            <p>
                For the frontend, I utilized HTML for structuring content, CSS for styling (including responsive design principles), and JavaScript for interactive elements and dynamic content updates. I also gained practical experience in configuring a local development environment using XAMPP and managing databases with phpMyAdmin. Understanding how Google OAuth 2.0 sign-in works was crucial for implementing third-party authentication, which involved setting up client IDs, secrets, and redirect URIs.
            </p>
            
            <p>
                Throughout the development process, I incorporated basic security ideas like password hashing and input validation to protect against common web vulnerabilities. I learned about prepared statements in PHP to prevent SQL injection attacks, and implemented proper session management to ensure user authentication is maintained securely. Finally, I learned how to use GitHub for version control, enabling me to upload, share, and collaborate on the project effectively.
            </p>
            
            <h3>Technology Stack</h3>
            
            <p>
                The project utilizes a modern web development stack:
            </p>
            
            <ul>
                <li><strong>Backend:</strong> PHP 7.4+ for server-side logic, MySQL for database management, and Apache web server (via XAMPP)</li>
                <li><strong>Frontend:</strong> HTML5 for structure, CSS3 with modern features (Flexbox, Grid, CSS Variables), and vanilla JavaScript for interactivity</li>
                <li><strong>Authentication:</strong> Google OAuth 2.0 for third-party sign-in, PHP sessions for maintaining user state</li>
                <li><strong>Database:</strong> MySQL with relational database design, including tables for users, cars, images, saved listings, and comments</li>
                <li><strong>Development Tools:</strong> XAMPP for local development, phpMyAdmin for database management, GitHub for version control</li>
            </ul>
            
            <h3>Design Decisions</h3>
            
            <p>
                Several key design decisions were made during development:
            </p>
            
            <ul>
                <li><strong>Responsive Design:</strong> The interface is designed to work seamlessly on mobile, tablet, and desktop devices using CSS media queries and flexible layouts</li>
                <li><strong>Glass Morphism UI:</strong> Modern glassmorphic design with backdrop blur effects and semi-transparent backgrounds for a contemporary look</li>
                <li><strong>Modular Architecture:</strong> Code is organized into separate files for maintainability (config.php, includes/, css/, js/ directories)</li>
                <li><strong>RESTful API Design:</strong> API endpoints follow REST principles for CRUD operations on listings and comments</li>
                <li><strong>Security First:</strong> All user inputs are validated and sanitized, passwords are hashed, and SQL queries use prepared statements</li>
            </ul>
            
            <h3>Implementation Challenges</h3>
            
            <p>
                During development, I encountered and solved several challenges:
            </p>
            
            <ul>
                <li><strong>Image Upload Handling:</strong> Implemented secure file upload validation, image processing, and storage in organized directories</li>
                <li><strong>Session Management:</strong> Properly configured PHP sessions to maintain user authentication across page loads</li>
                <li><strong>Database Relationships:</strong> Designed efficient database schema with proper foreign key relationships and indexes</li>
                <li><strong>OAuth Integration:</strong> Successfully integrated Google OAuth 2.0 with proper error handling and token management</li>
                <li><strong>Dynamic Content Loading:</strong> Implemented AJAX calls for saving listings, adding comments, and updating UI without page refreshes</li>
            </ul>
            
            <h3>References (APA)</h3>
            
            <div class="citation">
                <p>
                    W3Schools. (2024). <em>PHP Tutorial</em>. Retrieved from https://www.w3schools.com/php/
                </p>
            </div>
            
            <div class="citation">
                <p>
                    Mozilla Developer Network. (2024). <em>JavaScript Guide</em>. Retrieved from https://developer.mozilla.org/
                </p>
            </div>
            
            <div class="citation">
                <p>
                    Google. (2024). <em>Using OAuth 2.0 for Web Server Applications</em>. Retrieved from https://developers.google.com/identity
                </p>
            </div>
            
            <div class="citation">
                <p>
                    PHP Documentation Group. (2024). <em>PHP: Prepared Statements</em>. PHP Manual. Retrieved from https://www.php.net/manual/en/pdo.prepared-statements.php
                </p>
            </div>
            
            <div class="citation">
                <p>
                    MySQL AB. (2024). <em>MySQL 8.0 Reference Manual: Data Types</em>. Oracle Corporation. Retrieved from https://dev.mysql.com/doc/refman/8.0/en/data-types.html
                </p>
            </div>
            
            <div class="citation">
                <p>
                    W3Schools. (2024). <em>HTML Tutorial</em>. Retrieved from https://www.w3schools.com/html/
                </p>
            </div>
            
            <div class="citation">
                <p>
                    W3Schools. (2024). <em>CSS Tutorial</em>. Retrieved from https://www.w3schools.com/css/
                </p>
            </div>
            
            <div class="citation">
                <p>
                    Apache Friends. (2024). <em>XAMPP</em>. Retrieved from https://www.apachefriends.org/index.html
                </p>
            </div>
            
            <div class="citation">
                <p>
                    Google Identity. (2024). <em>OAuth 2.0</em>. Retrieved from https://developers.google.com/identity/protocols/oauth2
                </p>
            </div>
            
            <div class="citation">
                <p>
                    GitHub Docs. (2024). <em>About repositories</em>. Retrieved from https://docs.github.com/en/repositories/creating-and-managing-repositories/about-repositories
                </p>
            </div>
            
            <h3>References (IEEE)</h3>
            
            <div class="citation">
                <p>
                    [1] W3Schools, "PHP Tutorial," 2024. [Online]. Available: https://www.w3schools.com/php/
                </p>
            </div>
            
            <div class="citation">
                <p>
                    [2] Mozilla, "JavaScript Guide," MDN Web Docs, 2024. [Online]. Available: https://developer.mozilla.org/
                </p>
            </div>
            
            <div class="citation">
                <p>
                    [3] Google, "Using OAuth 2.0 for Web Server Applications," 2024. [Online]. Available: https://developers.google.com/identity
                </p>
            </div>
            
            <div class="citation">
                <p>
                    [4] PHP Documentation Group, "PHP: Prepared Statements," PHP Manual, 2024. [Online]. Available: https://www.php.net/manual/en/pdo.prepared-statements.php
                </p>
            </div>
            
            <div class="citation">
                <p>
                    [5] MySQL AB, "MySQL 8.0 Reference Manual: Data Types," Oracle Corporation, 2024. [Online]. Available: https://dev.mysql.com/doc/refman/8.0/en/data-types.html
                </p>
            </div>
            
            <div class="citation">
                <p>
                    [6] W3Schools, "HTML Tutorial," 2024. [Online]. Available: https://www.w3schools.com/html/
                </p>
            </div>
            
            <div class="citation">
                <p>
                    [7] W3Schools, "CSS Tutorial," 2024. [Online]. Available: https://www.w3schools.com/css/
                </p>
            </div>
            
            <div class="citation">
                <p>
                    [8] Apache Friends, "XAMPP," 2024. [Online]. Available: https://www.apachefriends.org/index.html
                </p>
            </div>
            
            <div class="citation">
                <p>
                    [9] Google Identity, "OAuth 2.0," 2024. [Online]. Available: https://developers.google.com/identity/protocols/oauth2
                </p>
            </div>
            
            <div class="citation">
                <p>
                    [10] GitHub Docs, "About repositories," 2024. [Online]. Available: https://docs.github.com/en/repositories/creating-and-managing-repositories/about-repositories
                </p>
            </div>
        </section>
        
        <section class="research-section">
            <div class="ai-disclosure">
                <h3>AI Use Disclosure</h3>
                <p>
                    Some parts of this project were created with assistance from AI tools.
                </p>
                <p>
                    I used ChatGPT (OpenAI) to get help with:
                </p>
                <ul>
                    <li>Debugging PHP and JavaScript errors</li>
                    <li>Understanding how Google OAuth works</li>
                    <li>Explaining configuration steps for XAMPP and phpMyAdmin</li>
                </ul>
                <p>
                    All generated code and text were reviewed, edited, and tested by us before being included in the project.
                </p>
                <p>
                    Final design choices, database structure, and implementation decisions were made by us.
                </p>
            </div>
        </section>
    </div>
    
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>

