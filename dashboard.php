<?php 
session_start();
include 'includes/db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/header.php'; 
?>

<style>
        :root {
            --primary: #e67e22;
            --primary-dark: #d35400;
            --bg-black: #080808;
            --card-bg: #121212;
            --text-gray: #b3b3b3;
        }

        .dashboard-page-wrap {
            background-color: var(--bg-black);
            color: #fff;
            font-family: 'Cairo', sans-serif;
            overflow-x: hidden;
        }

        .dashboard-header {
            padding: 2.5rem 5% 7rem;
            background: radial-gradient(circle at top left, rgba(230, 126, 34, 0.15), transparent);
            text-align: right;
        }

        .welcome-badge {
            background: rgba(230, 126, 34, 0.1);
            color: var(--primary);
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.9rem;
            border: 1px solid rgba(230, 126, 34, 0.2);
            display: inline-block;
            margin-bottom: 15px;
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -1px;
        }

        .dashboard-header h1 span {
            color: var(--primary);
            text-shadow: 0 0 20px rgba(230, 126, 34, 0.3);
        }

        .content-wrapper {
            max-width: 1200px;
            margin: -80px auto 50px;
            padding: 0 20px;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 60px;
        }

        .action-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            padding: 40px 30px;
            text-align: center;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(45deg, transparent, rgba(230, 126, 34, 0.05));
            opacity: 0;
            transition: 0.4s;
        }

        .action-card:hover {
            transform: translateY(-12px);
            border-color: var(--primary);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .action-card:hover::before { opacity: 1; }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: #1a1a1a;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: var(--primary);
            transition: 0.4s;
        }

        .action-card:hover .icon-wrapper {
            background: var(--primary);
            color: #000;
            transform: rotate(-10deg);
        }

        .action-card h3 {
            margin: 10px 0 5px;
            font-size: 1.3rem;
            color: #fff;
        }

        .action-card p {
            font-size: 0.9rem;
            color: var(--text-gray);
            margin: 0;
        }

        .destinations-section {
            background: var(--card-bg);
            border-radius: 30px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.03);
        }

        .section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 35px;
        }

        .section-title h2 {
            font-weight: 800;
            border-right: 5px solid var(--primary);
            padding-right: 15px;
        }

        .city-scroll {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding-bottom: 15px;
        }

        .city-scroll::-webkit-scrollbar { height: 5px; }
        .city-scroll::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }

        .city-card {
            min-width: 200px;
            text-align: center;
        }

        .city-card img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid transparent;
            transition: 0.4s;
            padding: 5px;
        }

        .city-card:hover img {
            border-color: var(--primary);
            transform: scale(1.05);
        }

        .city-card p {
            margin-top: 15px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .weather-section {
            background: linear-gradient(135deg, rgba(230, 126, 34, 0.15), rgba(230, 126, 34, 0.05));
            border-radius: 30px;
            padding: 40px;
            border: 2px solid rgba(230, 126, 34, 0.3);
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .weather-section::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -50%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(230, 126, 34, 0.1), transparent);
            border-radius: 50%;
            pointer-events: none;
        }

        .weather-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .weather-header h2 {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .weather-icon {
            font-size: 2.2rem;
            color: var(--primary);
        }

        .weather-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            position: relative;
            z-index: 1;
        }

        .weather-main {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(230, 126, 34, 0.2);
        }

        .temperature {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 10px;
        }

        .weather-condition {
            font-size: 1.2rem;
            color: var(--text-gray);
            margin-bottom: 20px;
            text-transform: capitalize;
        }

        .weather-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-label {
            color: var(--text-gray);
            font-size: 0.9rem;
        }

        .detail-value {
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
        }

        .weather-forecast {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(230, 126, 34, 0.2);
        }

        .forecast-title {
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--primary);
        }

        .forecast-items {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .forecast-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            background: rgba(230, 126, 34, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(230, 126, 34, 0.1);
            transition: 0.3s;
        }

        .forecast-item:hover {
            border-color: rgba(230, 126, 34, 0.3);
            background: rgba(230, 126, 34, 0.1);
        }

        .forecast-day {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .forecast-day-name {
            font-weight: 600;
            min-width: 80px;
        }

        .forecast-day-icon {
            font-size: 1.5rem;
            color: var(--primary);
            min-width: 30px;
        }

        .forecast-temps {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .forecast-temp-high {
            color: var(--primary);
            font-weight: 700;
        }

        .forecast-temp-low {
            color: var(--text-gray);
            font-size: 0.9rem;
        }

        .weather-loading {
            text-align: center;
            padding: 40px;
            color: var(--text-gray);
        }

        .weather-error {
            text-align: center;
            padding: 30px;
            color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(255, 107, 107, 0.2);
        }

        @media (max-width: 768px) {
            .weather-container {
                grid-template-columns: 1fr;
            }

            .temperature {
                font-size: 2.5rem;
            }

            .weather-section {
                padding: 25px;
            }
        }

    </style>

<div class="dashboard-page-wrap">
<header class="dashboard-header">
    <div class="welcome-badge">لوحة التحكم السياحية</div>
    <h1>أهلاً بك، <span><?php echo htmlspecialchars(explode(' ', (string)($_SESSION['user_name'] ?? ''))[0] ?? 'مسافر'); ?>!</span></h1>
    <p style="color: var(--text-gray);">اكتشف جمال الأردن مع MeshRider</p>
</header>

<div class="content-wrapper">
    <div class="action-grid">
        <a href="destinations.php" class="action-card">
            <div class="icon-wrapper"><i class="fas fa-map-pin"></i></div>
            <h3>الوجهات</h3>
            <p>دليل المعالم والرحلات</p>
        </a>

        <a href="trips.php" class="action-card">
            <div class="icon-wrapper"><i class="fas fa-map-marked-alt"></i></div>
            <h3>الرحلات</h3>
            <p>استكشف أفضل العروض</p>
        </a>

        <a href="generator.php" class="action-card">
            <div class="icon-wrapper"><i class="fas fa-wand-magic-sparkles"></i></div>
            <h3>البحث الذكي</h3>
            <p>ابحث حسب رغباتك</p>
        </a>

        <a href="my_bookings.php" class="action-card">
            <div class="icon-wrapper"><i class="fas fa-suitcase-rolling"></i></div>
            <h3>حجوزاتي</h3>
            <p>تتبع رحلاتك القادمة</p>
        </a>

        <a href="chat.php" class="action-card">
            <div class="icon-wrapper"><i class="fas fa-comments"></i></div>
            <h3>الدعم الفني</h3>
            <p>نحن هنا لمساعدتك دائماً</p>
        </a>

    </div>

    <div class="weather-section">
        <div class="weather-header">
            <h2><i class="fas fa-cloud-sun weather-icon"></i>الطقس الحالي</h2>
            <span style="color: var(--text-gray); font-size: 0.95rem;" id="weather-location">جاري التحديث...</span>
        </div>

        <div id="weather-content" class="weather-loading">
            <i class="fas fa-spinner fa-spin" style="color: var(--primary); font-size: 2rem; margin-bottom: 10px;"></i>
            <p>جاري جلب بيانات الطقس...</p>
        </div>
    </div>

    <div class="destinations-section">
        <div class="section-title">
            <h2>وجهات ينصح بها</h2>
            <a href="destinations.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">عرض الكل ←</a>
        </div>
        
        <div class="city-scroll">
            <div class="city-card">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQOZpGj7KW_Y1kcOo5H8550DOzFedwpsHBLfQ&s" alt="البتراء">
                <p>البتراء</p>
            </div>
            <div class="city-card">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQVj-YP8jma0_TF3sRSQcXXHTy6msJOMWScbQ&s" alt="وادي رم">
                <p>وادي رم</p>
            </div>
            <div class="city-card">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQTzGqkDfO-M1UoPiD9AE1bg_yb6t0308T5Sw&s" alt="العقبة">
                <p>العقبة</p>
            </div>
            <div class="city-card">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRAddEtu81RuRSULkURmNDA4ZBw52fqEAMheg&s" alt="البحر الميت">
                <p>البحر الميت</p>
            </div>
        </div>
    </div>
</div>
</div>
<?php include 'includes/footer.php'; ?>

<script>
    async function fetchWeather() {
        try {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    async (position) => {
                        const { latitude, longitude } = position.coords;
                        await getWeatherByCoordinates(latitude, longitude);
                    },
                    (error) => {
                        console.log('موقع غير متاح، استخدام الموقع الافتراضي');
                        getWeatherByCity('Amman');
                    }
                );
            } else {
                getWeatherByCity('Amman');
            }
        } catch (error) {
            console.error('خطأ في جلب الطقس:', error);
            showWeatherError();
        }
    }

    async function getWeatherByCoordinates(latitude, longitude) {
        const apiKey = 'e0c7e4a1e4b5f5c5d5e5f5a5b5c5d5e5'; 
        const weatherUrl = `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&current=temperature_2m,weather_code,relative_humidity_2m,weather_code,wind_speed_10m&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=auto`;
        const reverseGeoUrl = `https://geocoding-api.open-meteo.com/v1/reverse?latitude=${latitude}&longitude=${longitude}&language=ar`;
        
        try {
            const [weatherResponse, geoResponse] = await Promise.all([
                fetch(weatherUrl),
                fetch(reverseGeoUrl)
            ]);
            
            const weatherData = await weatherResponse.json();
            const geoData = await geoResponse.json();
            
            let locationData = null;
            if (geoData.results && geoData.results.length > 0) {
                locationData = geoData.results[0];
            }
            
            displayWeather(weatherData, latitude, longitude, locationData);
        } catch (error) {
            console.error('خطأ في API:', error);
            getWeatherByCity('Amman');
        }
    }

    async function getWeatherByCity(cityName) {
        const geoUrl = `https://geocoding-api.open-meteo.com/v1/search?name=${cityName}&language=ar&limit=1&format=json`;
        
        try {
            const geoResponse = await fetch(geoUrl);
            const geoData = await geoResponse.json();
            
            if (geoData.results && geoData.results.length > 0) {
                const { latitude, longitude } = geoData.results[0];
                const weatherUrl = `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&current=temperature_2m,weather_code,relative_humidity_2m,weather_code,wind_speed_10m&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=auto`;
                
                const weatherResponse = await fetch(weatherUrl);
                const weatherData = await weatherResponse.json();
                displayWeather(weatherData, latitude, longitude, geoData.results[0]);
            }
        } catch (error) {
            console.error('خطأ في جلب بيانات الموقع:', error);
            showWeatherError();
        }
    }

    function getWeatherInfo(weatherCode) {
        const weatherCodes = {
            0: { text: 'صافٍ', icon: '☀️' },
            1: { text: 'غائم جزئياً', icon: '🌤️' },
            2: { text: 'غائم', icon: '☁️' },
            3: { text: 'غائم جداً', icon: '☁️' },
            45: { text: 'ضباب', icon: '🌫️' },
            48: { text: 'ضباب بصقيع', icon: '🌫️' },
            51: { text: 'رذاذ خفيف', icon: '🌦️' },
            53: { text: 'رذاذ', icon: '🌦️' },
            55: { text: 'رذاذ كثيف', icon: '🌦️' },
            61: { text: 'أمطار خفيفة', icon: '🌧️' },
            63: { text: 'أمطار', icon: '🌧️' },
            65: { text: 'أمطار غزيرة', icon: '⛈️' },
            71: { text: 'ثلج خفيف', icon: '🌨️' },
            73: { text: 'ثلج', icon: '🌨️' },
            75: { text: 'ثلج كثيف', icon: '🌨️' },
            77: { text: 'حبات ثلجية', icon: '🌨️' },
            80: { text: 'أمطار خفيفة متقطعة', icon: '🌦️' },
            81: { text: 'أمطار متقطعة', icon: '🌧️' },
            82: { text: 'أمطار غزيرة متقطعة', icon: '⛈️' },
            85: { text: 'ثلج خفيف متقطع', icon: '🌨️' },
            86: { text: 'ثلج متقطع', icon: '🌨️' },
            95: { text: 'عاصفة رعدية', icon: '⛈️' },
            96: { text: 'عاصفة رعدية مع برد', icon: '⛈️' },
            99: { text: 'عاصفة رعدية مع ثلج', icon: '⛈️' }
        };
        return weatherCodes[weatherCode] || { text: 'غير محدد', icon: '🌤️' };
    }

    function getDayName(dateString) {
        const days = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        const date = new Date(dateString);
        return days[date.getDay()];
    }

    function displayWeather(data, latitude, longitude, locationData = null) {
        const current = data.current;
        const daily = data.daily;
        
        let locationName = 'موقعك الحالي';
        if (locationData) {
            locationName = locationData.name;
            if (locationData.admin1) {
                locationName += ', ' + locationData.admin1;
            }
        }

        let weatherContent = `
            <div class="weather-container">
                <div class="weather-main">
                    <div class="temperature">${Math.round(current.temperature_2m)}°C</div>
                    <div class="weather-condition">${getWeatherInfo(current.weather_code).icon} ${getWeatherInfo(current.weather_code).text}</div>
                    <div class="weather-details">
                        <div class="detail-item">
                            <i class="fas fa-droplet" style="color: var(--primary);"></i>
                            <div>
                                <span class="detail-label">الرطوبة</span>
                                <span class="detail-value">${current.relative_humidity_2m}%</span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-wind" style="color: var(--primary);"></i>
                            <div>
                                <span class="detail-label">سرعة الريح</span>
                                <span class="detail-value">${Math.round(current.wind_speed_10m)} كم/س</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="weather-forecast">
                    <div class="forecast-title"><i class="fas fa-calendar-days"></i> توقعات الأيام القادمة</div>
                    <div class="forecast-items">
        `;

        for (let i = 0; i < Math.min(3, daily.time.length); i++) {
            const date = daily.time[i];
            const dayName = getDayName(date);
            const weatherInfo = getWeatherInfo(daily.weather_code[i]);
            const tempMax = Math.round(daily.temperature_2m_max[i]);
            const tempMin = Math.round(daily.temperature_2m_min[i]);

            weatherContent += `
                        <div class="forecast-item">
                            <div class="forecast-day">
                                <span class="forecast-day-name">${dayName}</span>
                                <span class="forecast-day-icon">${weatherInfo.icon}</span>
                            </div>
                            <div class="forecast-temps">
                                <span class="forecast-temp-high">${tempMax}°</span>
                                <span class="forecast-temp-low">${tempMin}°</span>
                            </div>
                        </div>
            `;
        }

        weatherContent += `
                    </div>
                </div>
            </div>
        `;

        document.getElementById('weather-content').innerHTML = weatherContent;
        document.getElementById('weather-location').textContent = locationName;
    }

    function showWeatherError() {
        const errorContent = `
            <div class="weather-error">
                <i class="fas fa-exclamation-circle" style="margin-bottom: 10px; display: block; font-size: 1.5rem;"></i>
                <p>عذراً، لم نتمكن من جلب بيانات الطقس الحالية. يرجى محاولة لاحقاً.</p>
            </div>
        `;
        document.getElementById('weather-content').innerHTML = errorContent;
        document.getElementById('weather-location').textContent = 'غير محدد';
    }

    document.addEventListener('DOMContentLoaded', fetchWeather);

    setInterval(fetchWeather, 30 * 60 * 1000);

</script>

<?php include 'includes/footer.php'; ?>
