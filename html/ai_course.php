<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI電商課程 - 綻放 BloomWell</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* 专业UX优化样式 */
        :root {
            --primary-gradient: linear-gradient(135deg, #6B21A8 0%, #F472B6 100%);
            --secondary-gradient: linear-gradient(135deg, #6B21A8 0%, #2F4F4F 100%);
            --accent-gradient: linear-gradient(90deg, #6EE7B7 0%, #34D399 100%);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --card-shadow-hover: 0 15px 40px rgba(0, 0, 0, 0.15);
            --transition-all: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .section {
            margin: 5rem 0;
            position: relative;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }
        
        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--accent-color);
            border-radius: 2px;
        }
        
        .section-title p {
            font-size: 1.2rem;
            color: var(--dark-color);
            max-width: 700px;
            margin: 1rem auto 0;
        }
        
        .hero {
            background: url('https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80') no-repeat center center/cover;
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            padding: 2rem;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .hero p {
            font-size: 1.5rem;
            margin-bottom: 2.5rem;
            text-shadow: 0 1px 5px rgba(0, 0, 0, 0.3);
        }
        
        .btn-primary {
            background: var(--accent-gradient);
            color: var(--dark-color);
            border: none;
            padding: 1rem 2.5rem;
            font-size: 1.2rem;
            border-radius: 50px;
            cursor: pointer;
            transition: var(--transition-all);
            font-weight: 600;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
            margin-top: 2rem;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            padding: 2.5rem;
            transition: var(--transition-all);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover::before {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
            transition: var(--transition-all);
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.1);
        }
        
        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .feature-card p {
            color: var(--dark-color);
            line-height: 1.7;
        }
        
        .course-content {
            background: linear-gradient(135deg, #F5F5DC 0%, #E6E6FA 100%);
            padding: 5rem 0;
            margin: 5rem 0;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2.5rem;
            margin-top: 2rem;
        }
        
        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            transition: var(--transition-all);
            position: relative;
        }
        
        .content-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .content-card h3 {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--accent-color);
        }
        
        .content-card ul {
            list-style: none;
            padding: 0;
        }
        
        .content-card li {
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }
        
        .content-card li:last-child {
            border-bottom: none;
        }
        
        .content-card li::before {
            content: '✓';
            color: var(--accent-color);
            font-weight: bold;
            margin-right: 10px;
        }
        
        .pricing-section {
            margin: 5rem 0;
        }
        
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2.5rem;
            margin-top: 2rem;
        }
        
        .pricing-card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            padding: 2.5rem;
            text-align: center;
            transition: var(--transition-all);
            position: relative;
            overflow: hidden;
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .pricing-card.popular {
            border: 3px solid var(--accent-color);
            transform: scale(1.05);
        }
        
        .pricing-card.popular:hover {
            transform: scale(1.05) translateY(-10px);
        }
        
        .pricing-card.popular::after {
            content: '最推薦';
            position: absolute;
            top: 15px;
            right: -30px;
            background: var(--accent-color);
            color: var(--dark-color);
            padding: 5px 30px;
            transform: rotate(45deg);
            font-weight: bold;
            font-size: 0.8rem;
        }
        
        .pricing-card h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 1.5rem 0;
        }
        
        .pricing-card p {
            color: var(--dark-color);
            margin-bottom: 2rem;
        }
        
        .instructor-section {
            background: linear-gradient(135deg, #F5F5DC 0%, #E6E6FA 100%);
            padding: 5rem 0;
            margin: 5rem 0;
        }
        
        .instructor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
            margin-top: 2rem;
        }
        
        .instructor-card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            text-align: center;
            transition: var(--transition-all);
        }
        
        .instructor-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .instructor-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1.5rem;
            border: 5px solid var(--accent-color);
        }
        
        .instructor-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .instructor-card p {
            color: var(--dark-color);
            line-height: 1.7;
        }
        
        .cta-section {
            text-align: center;
            padding: 5rem 0;
            background: var(--secondary-gradient);
            color: white;
            margin: 5rem 0;
            border-radius: 15px;
        }
        
        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }
        
        .cta-section p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 2rem;
        }
        
        .testimonial-section {
            margin: 5rem 0;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: var(--card-shadow);
            margin: 2rem 0;
            position: relative;
            text-align: center;
            max-width: 800px;
            margin: 2rem auto;
        }
        
        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 10px;
            left: 20px;
            font-size: 5rem;
            color: var(--secondary-color);
            opacity: 0.2;
            font-family: serif;
        }
        
        .rating {
            color: #FBBF24;
            margin: 1.5rem 0;
            font-size: 1.2rem;
        }
        
        .faq-section {
            margin: 5rem 0;
        }
        
        .faq-grid {
            max-width: 800px;
            margin: 2rem auto 0;
        }
        
        .faq-item {
            background: white;
            border-radius: 10px;
            margin-bottom: 1rem;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }
        
        .faq-question {
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .faq-answer {
            padding: 0 1.5rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }
        
        .faq-item.active .faq-answer {
            padding: 0 1.5rem 1.5rem;
            max-height: 500px;
        }
        
        .faq-toggle {
            transition: transform 0.3s ease;
        }
        
        .faq-item.active .faq-toggle {
            transform: rotate(180deg);
        }
        
        .enroll-form {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            box-shadow: var(--card-shadow);
            max-width: 700px;
            margin: 3rem auto;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition-all);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(107, 33, 168, 0.1);
        }
        
        /* 移动端优化 */
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }
            
            .section {
                margin: 3rem 0;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .hero {
                height: 70vh;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1.2rem;
            }
            
            .features-grid,
            .content-grid,
            .pricing-grid,
            .instructor-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .pricing-card.popular {
                transform: scale(1);
            }
            
            .pricing-card.popular:hover {
                transform: scale(1) translateY(-5px);
            }
            
            .enroll-form {
                padding: 2rem 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="brand-logo">
            <img src="images/logo.svg" alt="綻放 BloomWell Logo" class="logo-icon">
            <div class="brand-name">
                <span class="chinese">綻放</span>
                <span class="english">BloomWell</span>
            </div>
        </div>
        <p>陪伴您舒適自在度過更年期，迎向人生下半場的健康美好生活</p>
    </header>
    
    <nav>
        <div class="container">
            <ul>
                <li><a href="index.html">首頁</a></li>
                <li><a href="index.html#knowledge">幸福熟齡</a></li>
                <li><a href="appointment.php">專家諮詢</a></li>
                <li><a href="login.php">會員登入</a></li>
            </ul>
        </div>
    </nav>
    
    <section class="hero">
        <div class="hero-content">
            <h1>AI電商課程</h1>
            <p>掌握AI技術，開創電商業務新藍海</p>
            <a href="#pricing" class="btn-primary">立即報名</a>
        </div>
    </section>
    
    <div class="container">
        <section class="section">
            <div class="section-title">
                <h2>為什麼選擇我們的AI電商課程？</h2>
                <p>專業設計的課程體系，助您快速掌握AI電商核心技能</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3>前沿技術</h3>
                    <p>學習最先進的AI技術在電商領域的應用，掌握市場趨勢</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3>實戰經驗</h3>
                    <p>由業界專家親授，分享真實案例與實戰經驗</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3>認證結業</h3>
                    <p>完成課程後獲得專業認證，提升職場競爭力</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3>變現機會</h3>
                    <p>掌握AI帶貨技能，開創被動收入來源</p>
                </div>
            </div>
        </section>
        
        <section class="section">
            <div class="section-title">
                <h2>商業模式創新</h2>
                <p>獨特的商業模式，實現從學習到變現的完整閉環</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>核心產品</h3>
                    <p>以愛健康iHealth的醫療級NMN抗衰產品為核心，結合AI電商技能培訓作為引流鉛磁</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>變現模式</h3>
                    <p>透過高分潤聯盟行銷制度變現，培養AI帶貨主播人才，轉化為加盟代理</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h3>被動收入</h3>
                    <p>實現被動收入目標：從陌生人引流→培養人才→轉化加盟→持續收益</p>
                </div>
            </div>
        </section>
    </div>
    
    <section class="course-content">
        <div class="container">
            <div class="section-title">
                <h2>課程大綱</h2>
                <p>從基礎到進階，系統化學習AI電商技能</p>
            </div>
            <div class="content-grid">
                <div class="content-card">
                    <h3>三天直播內容（AI電商初階課）</h3>
                    <ul>
                        <li>AI圖文/文案創作技巧</li>
                        <li>AI電商海報設計</li>
                        <li>AI帶貨短視頻製作</li>
                        <li>AI聯盟行銷玩法</li>
                        <li>接案細節與實務操作</li>
                    </ul>
                </div>
                
                <div class="content-card">
                    <h3>一天線下實作課程</h3>
                    <ul>
                        <li>實際電商平台AI整合</li>
                        <li>個性化推薦引擎實作</li>
                        <li>營銷自動化工具部署</li>
                        <li>數據分析與商業洞察</li>
                        <li>專家一對一指導</li>
                    </ul>
                </div>
                
                <div class="content-card">
                    <h3>加盟後專屬進階課程</h3>
                    <ul>
                        <li>加盟愛健康專屬權益</li>
                        <li>手把手教學指導</li>
                        <li>陪跑教練時間（一對一輔導）</li>
                        <li>專屬學習社群（同學交流與資源共享）</li>
                        <li>AI作圖技巧與應用</li>
                        <li>AI製作廣告影片</li>
                        <li>AI網站搭建與優化</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    
    <div class="container">
        <section id="pricing" class="section pricing-section">
            <div class="section-title">
                <h2>課程價格</h2>
                <p>靈活的價格方案，滿足不同學習需求</p>
            </div>
            <div class="pricing-grid">
                <div class="pricing-card">
                    <h3>線上課程</h3>
                    <div class="price">NT$ 8,800</div>
                    <p>三天線上課程，包含所有教材和錄影</p>
                    <ul>
                        <li><i class="fas fa-check"></i> 完整線上課程</li>
                        <li><i class="fas fa-check"></i> 課程教材</li>
                        <li><i class="fas fa-check"></i> 課程錄影</li>
                        <li><i class="fas fa-times"></i> 線下實作課程</li>
                        <li><i class="fas fa-times"></i> 專家一對一指導</li>
                    </ul>
                    <button class="btn-primary">立即購買</button>
                </div>
                
                <div class="pricing-card popular">
                    <h3>完整課程</h3>
                    <div class="price">NT$ 12,800</div>
                    <p>三天線上課程 + 一天線下實作課程</p>
                    <ul>
                        <li><i class="fas fa-check"></i> 完整線上課程</li>
                        <li><i class="fas fa-check"></i> 課程教材</li>
                        <li><i class="fas fa-check"></i> 課程錄影</li>
                        <li><i class="fas fa-check"></i> 線下實作課程</li>
                        <li><i class="fas fa-check"></i> 專家一對一指導</li>
                    </ul>
                    <button class="btn-primary">立即購買</button>
                </div>
            </div>
        </section>
        
        <section class="section testimonial-section">
            <div class="section-title">
                <h2>學員見證</h2>
                <p>聽聽我們學員的真實體驗</p>
            </div>
            <div class="testimonial-card">
                <p>這門課程讓我對AI在電商的應用有了全新的認識，講師的實戰經驗分享非常寶貴，課程內容深入淺出，讓我能夠快速掌握核心技術。</p>
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p><strong>陳經理</strong> - 電商公司技術主管</p>
            </div>
        </section>
        
        <section class="section instructor-section">
            <div class="section-title">
                <h2>講師團隊</h2>
                <p>業界頂尖專家親授，確保學習效果</p>
            </div>
            <div class="instructor-grid">
                <div class="instructor-card">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="張教授" class="instructor-avatar">
                    <h3>張教授</h3>
                    <p>國立台灣大學資訊工程學系教授，專精於人工智慧與機器學習領域超過15年。</p>
                </div>
                
                <div class="instructor-card">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="李總經理" class="instructor-avatar">
                    <h3>李總經理</h3>
                    <p>知名電商平台技術總監，帶領團隊實現AI技術在電商業務中的創新應用。</p>
                </div>
            </div>
        </section>
        
        <section class="section faq-section">
            <div class="section-title">
                <h2>常見問題</h2>
                <p>解答您對課程的疑問</p>
            </div>
            <div class="faq-grid">
                <div class="faq-item">
                    <div class="faq-question">
                        課程適合什麼樣的學員？
                        <i class="fas fa-chevron-down faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p>本課程適合對AI技術在電商應用有興趣的學員，包括電商從業人員、創業者、以及希望轉型進入AI領域的專業人士。</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        線上課程如何進行？
                        <i class="fas fa-chevron-down faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p>線上課程透過專屬平台進行，包含直播授課和錄播回看功能。學員可以隨時觀看課程內容，並參與線上討論。</p>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="section">
            <div class="section-title">
                <h2>立即購買</h2>
                <p>填寫以下表單完成報名</p>
            </div>
            <form class="enroll-form" action="process_course_enrollment.php" method="POST">
                <div class="form-group">
                    <label for="name">姓名</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="請輸入您的姓名" required>
                </div>
                
                <div class="form-group">
                    <label for="email">電子信箱</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="請輸入您的電子信箱" required>
                </div>
                
                <div class="form-group">
                    <label for="course_id">選擇課程方案</label>
                    <select id="course_id" name="course_id" class="form-control" required>
                        <option value="">請選擇課程方案</option>
                        <option value="1">AI電商初階課程 (NT$ 8,800)</option>
                        <option value="2">AI電商完整課程 (NT$ 12,800)</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%;">立即購買</button>
            </form>
        </section>
    </div>
    
    <footer>
        <div class="container">
            <p>© 2025 NmNiHealth 更年期健康保健養生專家. All rights reserved.</p>
            <p>本網站資訊僅供參考，不能替代專業醫療建議。如有嚴重症狀，請諮詢醫生。</p>
        </div>
    </footer>
    
    <script>
        // FAQ互動功能
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentNode;
                faqItem.classList.toggle('active');
            });
        });
    </script>
</body>
</html>