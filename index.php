<?php
session_start();
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DLCSMS | Premium Local Council Services</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .hero-visual {
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: url('assets/hero-bg.png') no-repeat center center;
            background-size: cover;
            z-index: -1;
            clip-path: polygon(25% 0%, 100% 0%, 100% 100%, 0% 100%);
            opacity: 0.9;
        }

        .service-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(6, 182, 212, 0.1));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary);
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .glass-card:hover .service-icon {
            background: var(--secondary);
            color: white;
            transform: scale(1.1) rotate(5deg);
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
            text-align: left;
        }

        .check-icon {
            width: 24px;
            height: 24px;
            background: var(--success);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .process-step {
            position: relative;
            padding: 40px;
            text-align: center;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            background: var(--secondary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            margin: 0 auto 20px;
            font-size: 1.2rem;
            box-shadow: var(--shadow-glow);
        }

        .faq-item {
            margin-bottom: 15px;
            padding: 24px;
            cursor: pointer;
        }

        .benefit-card {
            padding: 30px;
            border-radius: 20px;
            background: white;
            border: 1px solid #f1f5f9;
        }

        .stat-circle {
            width: 120px;
            height: 120px;
            border: 6px solid var(--secondary);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body class="animate-up">
    <nav class="container navbar">
        <a href="index.php" class="logo">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--secondary);"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            DLC<span>SMS</span>
        </a>
        <div class="nav-links" style="display: flex; gap: 24px; align-items: center;">
            <a href="#how-it-works" style="text-decoration: none; color: var(--text-main); font-weight: 600; font-size: 0.95rem;">Process</a>
            <a href="#services" style="text-decoration: none; color: var(--text-main); font-weight: 600; font-size: 0.95rem;">Services</a>
            <a href="#benefits" style="text-decoration: none; color: var(--text-main); font-weight: 600; font-size: 0.95rem;">Benefits</a>
            <?php if (is_logged_in()): ?>
                <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
            <?php else: ?>
                <a href="login.php" style="text-decoration: none; color: var(--text-main); font-weight: 600;">Sign In</a>
                <a href="register.php" class="btn btn-primary">Get Started</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-visual"></div>
        <div class="container" style="display: flex; align-items: center; min-height: 600px;">
            <div class="hero-content" style="text-align: left; margin: 0;">
                <span class="badge-new">Council Evolution 2026</span>
                <h1 style="font-size: 4.5rem; letter-spacing: -0.04em; margin-bottom: 24px;">Effortless <span style="display: block;">Council Services.</span></h1>
                <p class="text-muted" style="font-size: 1.25rem; max-width: 550px; margin-bottom: 40px; line-height: 1.5;">
                    The Digital Local Council Service Management System (DLCSMS) empowers citizens with a 24/7 digital portal for permits, certificates, and more.
                </p>
                <div style="display: flex; gap: 20px;">
                    <a href="register.php" class="btn btn-primary" style="padding: 18px 36px; font-size: 1.1rem; border-radius: var(--radius-md);">Initialize Application</a>
                    <a href="#how-it-works" class="btn btn-secondary" style="padding: 18px 36px; font-size: 1.1rem; border-radius: var(--radius-md);">Learn More</a>
                </div>
                
                <div style="display: flex; gap: 40px; margin-top: 60px;">
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">24/7</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Portal Access</div>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">98%</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Success Rate</div>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">Secure</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Cloud Data</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="how-it-works" style="padding: 120px 0; background: white;">
        <div class="container">
            <div style="text-align: center; max-width: 800px; margin: 0 auto 80px;">
                <h2 style="font-size: 3rem; margin-bottom: 20px;">How It Works</h2>
                <p class="text-muted">A streamlined 4-step process to get your council services processed without leaving your home.</p>
            </div>
            
            <div class="grid-3" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
                <div class="process-step">
                    <div class="step-number">01</div>
                    <h3 style="margin-bottom: 12px;">Register Account</h3>
                    <p class="text-muted">Create your secure digital profile in less than 2 minutes using your email.</p>
                </div>
                <div class="process-step">
                    <div class="step-number">02</div>
                    <h3 style="margin-bottom: 12px;">Submit Request</h3>
                    <p class="text-muted">Select your service node and provide the necessary telemetry data and details.</p>
                </div>
                <div class="process-step">
                    <div class="step-number">03</div>
                    <h3 style="margin-bottom: 12px;">Track Protocol</h3>
                    <p class="text-muted">Monitor your application status in real-time through your personal command center.</p>
                </div>
                <div class="process-step">
                    <div class="step-number">04</div>
                    <h3 style="margin-bottom: 12px;">Receive Document</h3>
                    <p class="text-muted">Once approved, download your official council document directly from the portal.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="services" style="padding: 120px 0; background: var(--surface-muted);">
        <div class="container">
            <div style="text-align: center; max-width: 700px; margin: 0 auto 80px;">
                <h2 style="font-size: 3rem; margin-bottom: 20px;">Dedicated Service Nodes</h2>
                <p class="text-muted">Access a comprehensive suite of council functions designed for speed and precision.</p>
            </div>
            
            <div class="grid-3">
                <div class="glass-card" style="padding: 40px;">
                    <div class="service-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    </div>
                    <h3>Business & Trading</h3>
                    <p class="text-muted" style="margin: 16px 0 24px;">Manage business registrations and trading permits that are vital for local economic participation.</p>
                    <ul style="list-style: none; padding: 0;">
                        <li class="feature-item"><span class="check-icon" style="background:var(--secondary)">✓</span> <span style="font-size: 0.9rem; font-weight: 500;">Retail Trading Permits</span></li>
                        <li class="feature-item"><span class="check-icon" style="background:var(--secondary)">✓</span> <span style="font-size: 0.9rem; font-weight: 500;">Business Operations License</span></li>
                    </ul>
                </div>

                <div class="glass-card" style="padding: 40px;">
                    <div class="service-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    </div>
                    <h3>Vital Registrations</h3>
                    <p class="text-muted" style="margin: 16px 0 24px;">Official certification for life events ensuring your legal status is always current and verifiable.</p>
                    <ul style="list-style: none; padding: 0;">
                        <li class="feature-item"><span class="check-icon" style="background:var(--accent)">✓</span> <span style="font-size: 0.9rem; font-weight: 500;">Birth Records Application</span></li>
                        <li class="feature-item"><span class="check-icon" style="background:var(--accent)">✓</span> <span style="font-size: 0.9rem; font-weight: 500;">Marriage Certification</span></li>
                    </ul>
                </div>

                <div class="glass-card" style="padding: 40px;">
                    <div class="service-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    </div>
                    <h3>Property & Logistics</h3>
                    <p class="text-muted" style="margin: 16px 0 24px;">Handle complex land registration and building permits through our streamlined approval engine.</p>
                    <ul style="list-style: none; padding: 0;">
                        <li class="feature-item"><span class="check-icon" style="background:var(--primary)">✓</span> <span style="font-size: 0.9rem; font-weight: 500;">Construction Approvals</span></li>
                        <li class="feature-item"><span class="check-icon" style="background:var(--primary)">✓</span> <span style="font-size: 0.9rem; font-weight: 500;">Land Title Registration</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="benefits" style="padding: 120px 0; background: white;">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;">
                <div>
                    <h2 style="font-size: 3rem; margin-bottom: 32px;">Why Citizens Choose Digital First</h2>
                    <p class="text-muted" style="font-size: 1.1rem; margin-bottom: 40px;">The DLCSMS ecosystem provides a more transparent, accountable, and faster way for you to interact with your local government.</p>
                    
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <div class="benefit-card">
                            <h4 style="margin-bottom: 8px; color: var(--secondary);">Zero Paperwork</h4>
                            <p class="text-muted">Everything from initial application to final approval is handled digitally, saving time and resources.</p>
                        </div>
                        <div class="benefit-card">
                            <h4 style="margin-bottom: 8px; color: var(--accent);">Real-Time Alerts</h4>
                            <p class="text-muted">Receive instant feedback on your application status via notifications—no more guessing.</p>
                        </div>
                        <div class="benefit-card">
                            <h4 style="margin-bottom: 8px; color: var(--primary);">Secure Environment</h4>
                            <p class="text-muted">Your personal data is encrypted and managed with bank-grade security protocols.</p>
                        </div>
                    </div>
                </div>
                
                <div class="glass-card" style="padding: 60px; text-align: center;">
                    <h3 style="margin-bottom: 40px;">Platform Statistics 2026</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                        <div>
                            <div class="stat-circle"><div style="font-size: 1.5rem; font-weight: 800;">12k+</div><div style="font-size: 0.7rem; font-weight: 700;">USERS</div></div>
                            <p style="font-size: 0.9rem; font-weight: 600;">Active Citizens</p>
                        </div>
                        <div>
                            <div class="stat-circle" style="border-color: var(--accent);"><div style="font-size: 1.5rem; font-weight: 800;">45k+</div><div style="font-size: 0.7rem; font-weight: 700;">APPS</div></div>
                            <p style="font-size: 0.9rem; font-weight: 600;">Processed Requests</p>
                        </div>
                        <div>
                            <div class="stat-circle" style="border-color: var(--success);"><div style="font-size: 1.5rem; font-weight: 800;">2.5h</div><div style="font-size: 0.7rem; font-weight: 700;">AVG</div></div>
                            <p style="font-size: 0.9rem; font-weight: 600;">Review Time</p>
                        </div>
                        <div>
                            <div class="stat-circle" style="border-color: var(--primary);"><div style="font-size: 1.5rem; font-weight: 800;">100%</div><div style="font-size: 0.7rem; font-weight: 700;">ONLINE</div></div>
                            <p style="font-size: 0.9rem; font-weight: 600;">System Uptime</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="faq" style="padding: 120px 0; background: var(--surface-muted);">
        <div class="container" style="max-width: 900px;">
            <div style="text-align: center; margin-bottom: 60px;">
                <h2 style="font-size: 2.5rem;">Common Questions</h2>
                <p class="text-muted">Find quick answers to common inquiries about the digital council portal.</p>
            </div>
            
            <div class="faq-list">
                <div class="glass-card faq-item">
                    <h4 style="margin-bottom: 8px;">What documents are required for building permits?</h4>
                    <p class="text-muted" style="font-size: 0.95rem;">Building permits require architectural drawings, structural integrity reports, and proof of land ownership. You can upload these directly during the application protocol.</p>
                </div>
                <div class="glass-card faq-item">
                    <h4 style="margin-bottom: 8px;">How long does the review process take?</h4>
                    <p class="text-muted" style="font-size: 0.95rem;">The average primary review time is 2-4 hours. Depending on the complexity and volume, the finalize protocol may take up to 48 business hours.</p>
                </div>
                <div class="glass-card faq-item">
                    <h4 style="margin-bottom: 8px;">Is my personal data secure?</h4>
                    <p class="text-muted" style="font-size: 0.95rem;">Yes, DLCSMS uses high-level encryption protocols and secure server environments to ensure that all citizen telemetry data is protected according to national standards.</p>
                </div>
                <div class="glass-card faq-item">
                    <h4 style="margin-bottom: 8px;">Can I download old approved documents?</h4>
                    <p class="text-muted" style="font-size: 0.95rem;">Absolutely. Your digital command center maintains a permanent record of all approved service nodes, available for download at any time.</p>
                </div>
            </div>
        </div>
    </section>

    <section style="padding: 100px 0; background: var(--primary); color: white; border-radius: 60px 60px 0 0;">
        <div class="container text-center">
            <h2 style="color: white; font-size: 3rem; margin-bottom: 32px;">Advance to Digital Governance</h2>
            <p style="color: rgba(255,255,255,0.7); font-size: 1.2rem; max-width: 600px; margin: 0 auto 48px;">Experience the next generation of local council interactions for your community.</p>
            <a href="register.php" class="btn btn-primary" style="background: white; color: var(--primary); padding: 20px 48px; font-size: 1.2rem;">Create Digital Identity</a>
        </div>
    </section>

    <footer style="background: var(--primary); color: white; border-top: 1px solid rgba(255,255,255,0.1); padding: 100px 0 40px;">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 80px; margin-bottom: 60px;">
                <div>
                    <a href="index.php" class="logo" style="color: white; margin-bottom: 24px;">DLC<span>SMS</span></a>
                    <p style="color: rgba(255,255,255,0.6); max-width: 300px;">Providing secure and efficient digital governance for local councils across the nation. Advancing the digital frontier.</p>
                </div>
                <div>
                    <h4 style="color: white; margin-bottom: 24px;">Platform</h4>
                    <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 12px;">
                        <li><a href="#" style="color: rgba(255,255,255,0.6); text-decoration: none;">How it Works</a></li>
                        <li><a href="#" style="color: rgba(255,255,255,0.6); text-decoration: none;">Service Nodes</a></li>
                        <li><a href="#" style="color: rgba(255,255,255,0.6); text-decoration: none;">System Status</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="color: white; margin-bottom: 24px;">Resources</h4>
                    <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 12px;">
                        <li><a href="#" style="color: rgba(255,255,255,0.6); text-decoration: none;">Help Center</a></li>
                        <li><a href="#" style="color: rgba(255,255,255,0.6); text-decoration: none;">Public Records</a></li>
                        <li><a href="#" style="color: rgba(255,255,255,0.6); text-decoration: none;">Contact Council</a></li>
                    </ul>
                </div>
            </div>
            <div style="text-align: center; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.4); font-size: 0.9rem;">
                <p>&copy; 2026 Digital Local Council Service Management System. The Standards of Digital Sovereignty.</p>
            </div>
        </div>
    </footer>
</body>
</html>
