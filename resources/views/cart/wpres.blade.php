



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .hero-section {
            min-height: 80vh;
            background: #FFFFFF;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            font-family: 'Arial', sans-serif;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 900;
            text-align: center;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-title .yellow {
            color: #FFD700;
        }

        .hero-title .white {
            color: #1a1a1a;
        }

        .hero-subtitle {
            color: #333333;
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 60px;
            font-weight: 300;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
            margin-bottom: 60px;
            max-width: 900px;
        }

        .feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #DC143C;
            background: rgba(220, 20, 60, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .feature-icon:hover {
            transform: scale(1.1);
        }

        .feature-icon svg {
            width: 40px;
            height: 40px;
            fill: #FFD700;
        }

        .feature-label {
            color: #1a1a1a;
            font-size: 1rem;
            font-weight: 500;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
        }

        .btn {
            padding: 18px 40px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-yellow {
            background: #FFD700;
            color: #1a1a1a;
        }

        .btn-yellow:hover {
            background: #FFC700;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 215, 0, 0.3);
        }

        .btn-pink {
            background: #DC143C;
            color: #FFFFFF;
        }

        .btn-pink:hover {
            background: #C41230;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(220, 20, 60, 0.3);
        }

        .scroll-indicator {
            position: absolute;
            bottom: 40px;
            animation: bounce 2s infinite;
        }

        .scroll-arrow {
            width: 0;
            height: 0;
            border-left: 25px solid transparent;
            border-right: 25px solid transparent;
            border-top: 30px solid #FFD700;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.2rem;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 30px;
            }

            .cta-buttons {
                flex-direction: column;
                width: 100%;
                max-width: 300px;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <section class="hero-section">
        <h1 class="hero-title">
            <span class="yellow">Bienvenue</span> <span class="white">sur FC Showbiz</span>
        </h1>
        
        <p class="hero-subtitle">Votre portail N°1 du Showbiz Ivoirien et Africain</p>
        
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                    </svg>
                </div>
                <span class="feature-label">Actualités</span>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                </div>
                <span class="feature-label">Annuaire Artistes</span>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                    </svg>
                </div>
                <span class="feature-label">Événements</span>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M7 3V1h2v2H7zm0 18v2h2v-2H7zm11-7c0 1.73-1.41 3.14-3.14 3.14-.41 0-.81-.08-1.19-.23l-1.87 1.87c.45.67.71 1.48.71 2.36H11c0-1.65-1.35-3-3-3s-3 1.35-3 3H3c0-1.65-1.35-3-3-3v-2c1.65 0 3-1.35 3-3s-1.35-3-3-3V7c1.65 0 3-1.35 3-3h2c0 1.65 1.35 3 3 3s3-1.35 3-3h2c0 .88-.26 1.69-.71 2.36l1.87 1.87c.38-.15.78-.23 1.19-.23C16.59 7.86 18 9.27 18 11z"/>
                    </svg>
                </div>
                <span class="feature-label">Votes & Célébrités</span>
            </div>
        </div>
        
        <div class="cta-buttons">
            <button class="btn btn-yellow">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                </svg>
                Découvrir les Articles
            </button>
            <button class="btn btn-pink">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                Accéder à l'Annuaire
            </button>
        </div>
        
        <div class="scroll-indicator">
            <div class="scroll-arrow"></div>
        </div>
    </section>
</body>
</html>