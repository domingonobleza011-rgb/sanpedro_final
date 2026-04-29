<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Hotlines - Barangay Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header .emergency-badge {
            display: inline-block;
            background: #ff4444;
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 68, 68, 0.7);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 20px 10px rgba(255, 68, 68, 0);
            }
        }

        .quick-dial {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .quick-dial-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .quick-dial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .quick-dial-card.emergency {
            background: linear-gradient(135deg, #ff4444 0%, #cc0000 100%);
            color: white;
        }

        .quick-dial-card.police {
            background: linear-gradient(135deg, #2196F3 0%, #0D47A1 100%);
            color: white;
        }

        .quick-dial-card.fire {
            background: linear-gradient(135deg, #FF5722 0%, #D84315 100%);
            color: white;
        }

        .quick-dial-card.medical {
            background: linear-gradient(135deg, #4CAF50 0%, #1B5E20 100%);
            color: white;
        }

        .quick-dial-icon {
            font-size: 3em;
            margin-bottom: 15px;
        }

        .quick-dial-card h3 {
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .quick-dial-card .number {
            font-size: 1.8em;
            font-weight: bold;
            margin-top: 10px;
        }

        .section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
        }

        .section h2 {
            color: #333;
            font-size: 1.8em;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .contact-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
        }

        .contact-card:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .contact-card h3 {
            color: #333;
            font-size: 1.3em;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .contact-info {
            margin: 10px 0;
        }

        .contact-info i {
            color: #667eea;
            width: 25px;
        }

        .contact-info span {
            color: #666;
            margin-left: 5px;
        }

        .call-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #667eea;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 15px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .call-button:hover {
            background: #5568d3;
            transform: scale(1.05);
        }

        .call-button.emergency-call {
            background: #ff4444;
        }

        .call-button.emergency-call:hover {
            background: #cc0000;
        }

        .info-box {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .info-box h4 {
            color: #856404;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-box ul {
            margin-left: 35px;
            color: #856404;
        }

        .info-box ul li {
            margin: 5px 0;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8em;
            }

            .quick-dial {
                grid-template-columns: 1fr;
            }

            .contact-grid {
                grid-template-columns: 1fr;
            }

            .quick-dial-card .number {
                font-size: 1.5em;
            }
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: white;
            color: #667eea;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: #667eea;
            color: white;
            transform: translateX(-5px);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <div class="header">
            <h1><i class="fas fa-phone-volume"></i> Emergency Hotlines</h1>
            <div class="emergency-badge">
                <i class="fas fa-exclamation-triangle"></i> AVAILABLE 24/7
            </div>
        </div>

        <!-- Quick Dial Section -->
        <div class="quick-dial">
            <a href="tel:911" class="quick-dial-card emergency">
                <div class="quick-dial-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <h3>Emergency</h3>
                <div class="number">911</div>
                <p>National Emergency</p>
            </a>

            <a href="tel:117" class="quick-dial-card police">
                <div class="quick-dial-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Police</h3>
                <div class="number">117</div>
                <p>PNP Hotline</p>
            </a>

            <a href="tel:160" class="quick-dial-card fire">
                <div class="quick-dial-icon">
                    <i class="fas fa-fire-extinguisher"></i>
                </div>
                <h3>Fire</h3>
                <div class="number">160</div>
                <p>BFP Emergency</p>
            </a>

            <a href="tel:143" class="quick-dial-card medical">
                <div class="quick-dial-icon">
                    <i class="fas fa-ambulance"></i>
                </div>
                <h3>Red Cross</h3>
                <div class="number">143</div>
                <p>Medical Emergency</p>
            </a>
        </div>

        <!-- Barangay Emergency Contacts -->
        <div class="section">
            <h2><i class="fas fa-building"></i> Barangay Emergency Contacts</h2>
            <div class="contact-grid">
                <div class="contact-card">
                    <h3><i class="fas fa-user-tie"></i> Barangay Captain</h3>
                    <div class="contact-info">
                        <i class="fas fa-user"></i>
                        <span>Hon. Juan Dela Cruz</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 907 056 9634</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-envelope"></i>
                        <span>captain@barangay.gov.ph</span>
                    </div>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-users"></i> Barangay Emergency Response Team</h3>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 917 234 5678</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-mobile-alt"></i>
                        <span>+63 918 234 5678</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-clock"></i>
                        <span>24/7 Availability</span>
                    </div>
                    <a href="tel:+639172345678" class="call-button emergency-call">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-shield-alt"></i> Barangay Tanod</h3>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 917 345 6789</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Barangay Hall</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-clock"></i>
                        <span>24/7 Patrol</span>
                    </div>
                    <a href="tel:+639173456789" class="call-button">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-heartbeat"></i> Barangay Health Center</h3>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 917 456 7890</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Main Street, Barangay Hall Compound</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-clock"></i>
                        <span>Mon-Sat: 8AM-5PM</span>
                    </div>
                    <a href="tel:+639174567890" class="call-button">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>
            </div>
        </div>

        <!-- Nearest Hospital -->
        <div class="section">
            <h2><i class="fas fa-hospital"></i> Nearest Hospitals</h2>
            <div class="contact-grid">
                <div class="contact-card">
                    <h3><i class="fas fa-hospital-alt"></i> City General Hospital</h3>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>123 Hospital Road, City Center</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 2 1234 5678</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-ambulance"></i>
                        <span>Emergency: +63 917 567 8901</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-road"></i>
                        <span>Distance: 2.5 km</span>
                    </div>
                    <a href="tel:+639175678901" class="call-button emergency-call">
                        <i class="fas fa-ambulance"></i> Call Emergency
                    </a>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-hospital"></i> St. Mary's Medical Center</h3>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>456 Medical Avenue, Downtown</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 2 2345 6789</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-ambulance"></i>
                        <span>Emergency: +63 917 678 9012</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-road"></i>
                        <span>Distance: 3.8 km</span>
                    </div>
                    <a href="tel:+639176789012" class="call-button emergency-call">
                        <i class="fas fa-ambulance"></i> Call Emergency
                    </a>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-clinic-medical"></i> Community Health Clinic</h3>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>789 Community Street</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 917 789 0123</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-clock"></i>
                        <span>24/7 Emergency Care</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-road"></i>
                        <span>Distance: 1.2 km</span>
                    </div>
                    <a href="tel:+639177890123" class="call-button">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>
            </div>
        </div>

        <!-- Police Stations -->
        <div class="section">
            <h2><i class="fas fa-shield-alt"></i> Police Stations</h2>
            <div class="contact-grid">
                <div class="contact-card">
                    <h3><i class="fas fa-building-shield"></i> City Police Station 1</h3>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Police Complex, Main Boulevard</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 2 3456 7890</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Emergency: +63 917 890 1234</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-road"></i>
                        <span>Distance: 1.8 km</span>
                    </div>
                    <a href="tel:+639178901234" class="call-button emergency-call">
                        <i class="fas fa-phone"></i> Call Emergency
                    </a>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-building-shield"></i> Women and Children Protection Desk</h3>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>City Police Station 1</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 917 901 2345</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-clock"></i>
                        <span>24/7 Availability</span>
                    </div>
                    <a href="tel:+639179012345" class="call-button">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>
            </div>
        </div>

        <!-- Fire Stations -->
        <div class="section">
            <h2><i class="fas fa-fire-extinguisher"></i> Fire Stations</h2>
            <div class="contact-grid">
                <div class="contact-card">
                    <h3><i class="fas fa-truck-pickup"></i> City Fire Station</h3>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Fire Station Road, City Center</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 2 4567 8901</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Emergency: +63 918 012 3456</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-road"></i>
                        <span>Distance: 2.0 km</span>
                    </div>
                    <a href="tel:+639180123456" class="call-button emergency-call">
                        <i class="fas fa-fire-extinguisher"></i> Call Emergency
                    </a>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-truck-pickup"></i> Volunteer Fire Brigade</h3>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Barangay Hall Annex</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 918 123 4567</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-clock"></i>
                        <span>24/7 Response Team</span>
                    </div>
                    <a href="tel:+639181234567" class="call-button">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>
            </div>
        </div>

        <!-- Other Important Contacts -->
        <div class="section">
            <h2><i class="fas fa-address-book"></i> Other Important Contacts</h2>
            <div class="contact-grid">
                <div class="contact-card">
                    <h3><i class="fas fa-bolt"></i> Electric Company</h3>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 2 5678 9012</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Emergency: +63 918 234 5678</span>
                    </div>
                    <a href="tel:+639182345678" class="call-button">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-tint"></i> Water District</h3>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 2 6789 0123</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Emergency: +63 918 345 6789</span>
                    </div>
                    <a href="tel:+639183456789" class="call-button">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-cloud-rain"></i> Disaster Risk Reduction Office</h3>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 918 456 7890</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-clock"></i>
                        <span>24/7 Monitoring</span>
                    </div>
                    <a href="tel:+639184567890" class="call-button">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-paw"></i> Animal Bite Center</h3>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 918 567 8901</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>City Health Office</span>
                    </div>
                    <a href="tel:+639185678901" class="call-button">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-balance-scale"></i> Public Attorney's Office</h3>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 2 7890 1234</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-clock"></i>
                        <span>Mon-Fri: 8AM-5PM</span>
                    </div>
                    <a href="tel:+6327890123" class="call-button">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-hands-helping"></i> Social Welfare Office</h3>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+63 918 678 9012</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-envelope"></i>
                        <span>dswd@barangay.gov.ph</span>
                    </div>
                    <a href="tel:+639186789012" class="call-button">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>
            </div>
        </div>

        <!-- Emergency Tips -->
        <div class="section">
            <h2><i class="fas fa-info-circle"></i> Emergency Guidelines</h2>
            <div class="info-box">
                <h4><i class="fas fa-exclamation-triangle"></i> When to Call Emergency Services:</h4>
                <ul>
                    <li>Medical emergencies (heart attack, severe bleeding, unconsciousness)</li>
                    <li>Fire incidents or smell of gas</li>
                    <li>Crimes in progress or immediate threats to safety</li>
                    <li>Serious accidents with injuries</li>
                    <li>Natural disasters (flooding, earthquakes, typhoons)</li>
                </ul>
            </div>

            <div class="info-box" style="margin-top: 20px;">
                <h4><i class="fas fa-lightbulb"></i> Important Reminders:</h4>
                <ul>
                    <li>Stay calm and speak clearly when calling emergency services</li>
                    <li>Provide your exact location and nature of emergency</li>
                    <li>Follow instructions given by the emergency operator</li>
                    <li>Keep these numbers saved in your mobile phone</li>
                    <li>Teach children how to dial emergency numbers</li>
                    <li>Do not hang up until instructed to do so</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Add click tracking for analytics
        document.querySelectorAll('.call-button, .quick-dial-card').forEach(button => {
            button.addEventListener('click', function(e) {
                const contactName = this.querySelector('h3') ? 
                    this.querySelector('h3').textContent : 
                    'Quick Dial';
                console.log(`Emergency call initiated to: ${contactName}`);
                // You can add analytics tracking here
            });
        });

        // Add accessibility features
        document.querySelectorAll('a[href^="tel:"]').forEach(link => {
            link.setAttribute('aria-label', `Call ${link.textContent.trim()}`);
        });

        // Optional: Add geolocation for nearest facility
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    console.log('User location:', position.coords);
                    // You can use this to calculate and display nearest facilities
                },
                error => {
                    console.log('Location access denied');
                }
            );
        }
    </script>
</body>
</html>