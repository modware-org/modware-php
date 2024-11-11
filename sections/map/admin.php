<div class="content-header">
    <h1>Map Settings</h1>
</div>

<div class="card">
    <form id="mapSettingsForm" onsubmit="handleMapSettingsSubmit(event)">
        <div class="form-group">
            <label>Map Status</label>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="mapActive" name="is_active">
                <label class="custom-control-label" for="mapActive">Active</label>
            </div>
        </div>

        <div class="form-group">
            <label for="latitude">Latitude</label>
            <input type="text" id="latitude" name="latitude" class="form-control" required 
                   pattern="-?\d+(\.\d+)?" title="Please enter a valid latitude">
            <small class="form-text text-muted">Example: 52.2317604</small>
        </div>

        <div class="form-group">
            <label for="longitude">Longitude</label>
            <input type="text" id="longitude" name="longitude" class="form-control" required
                   pattern="-?\d+(\.\d+)?" title="Please enter a valid longitude">
            <small class="form-text text-muted">Example: 21.0172998</small>
        </div>

        <div class="form-group">
            <label for="zoom">Zoom Level</label>
            <input type="number" id="zoom" name="zoom" class="form-control" min="1" max="20" required>
            <small class="form-text text-muted">Values between 1 and 20 (higher number = more zoomed in)</small>
        </div>

        <div class="form-group">
            <label for="markerTitle">Marker Title</label>
            <input type="text" id="markerTitle" name="marker_title" class="form-control" required>
            <small class="form-text text-muted">Title shown when hovering over the map marker</small>
        </div>

        <div class="form-group">
            <label for="apiKey">Google Maps API Key</label>
            <div class="input-group">
                <input type="text" id="apiKey" name="api_key" class="form-control" required>
                <button type="button" class="btn btn-secondary" onclick="toggleApiKeyVisibility()">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <small class="form-text text-muted">Your Google Maps JavaScript API key</small>
        </div>

        <div class="map-preview">
            <h3>Map Preview</h3>
            <div id="mapPreview" style="height: 400px; margin-bottom: 20px;"></div>
            <button type="button" class="btn btn-secondary" onclick="updateMapPreview()">Update Preview</button>
        </div>

        <button type="submit" class="btn btn-primary mt-4">Save Settings</button>
    </form>
</div>

<script>
let map, marker;

document.addEventListener('DOMContentLoaded', () => {
    loadMapSettings();
    initializeMapPreview();
});

async function loadMapSettings() {
    try {
        const settings = await handleApiRequest('map/settings');
        document.getElementById('mapActive').checked = settings.is_active === 1;
        document.getElementById('latitude').value = settings.latitude;
        document.getElementById('longitude').value = settings.longitude;
        document.getElementById('zoom').value = settings.zoom;
        document.getElementById('markerTitle').value = settings.marker_title;
        document.getElementById('apiKey').value = settings.api_key;
        
        if (map && marker) {
            updateMapPreview();
        }
    } catch (error) {
        console.error('Error loading map settings:', error);
        showError('Failed to load map settings');
    }
}

function initializeMapPreview() {
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${document.getElementById('apiKey').value}&callback=initMap`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}

function initMap() {
    const lat = parseFloat(document.getElementById('latitude').value) || 52.2317604;
    const lng = parseFloat(document.getElementById('longitude').value) || 21.0172998;
    const zoom = parseInt(document.getElementById('zoom').value) || 15;

    map = new google.maps.Map(document.getElementById('mapPreview'), {
        center: { lat, lng },
        zoom: zoom
    });

    marker = new google.maps.Marker({
        position: { lat, lng },
        map: map,
        title: document.getElementById('markerTitle').value
    });

    map.addListener('click', (e) => {
        const lat = e.latLng.lat();
        const lng = e.latLng.lng();
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        marker.setPosition(e.latLng);
    });
}

function updateMapPreview() {
    if (!map || !marker) return;

    const lat = parseFloat(document.getElementById('latitude').value);
    const lng = parseFloat(document.getElementById('longitude').value);
    const zoom = parseInt(document.getElementById('zoom').value);
    const title = document.getElementById('markerTitle').value;

    const position = new google.maps.LatLng(lat, lng);
    map.setCenter(position);
    map.setZoom(zoom);
    marker.setPosition(position);
    marker.setTitle(title);
}

function toggleApiKeyVisibility() {
    const input = document.getElementById('apiKey');
    const icon = document.querySelector('.btn-secondary i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

async function handleMapSettingsSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            is_active: formData.get('is_active') === 'on' ? 1 : 0,
            latitude: parseFloat(formData.get('latitude')),
            longitude: parseFloat(formData.get('longitude')),
            zoom: parseInt(formData.get('zoom')),
            marker_title: formData.get('marker_title'),
            api_key: formData.get('api_key')
        };

        await handleApiRequest('map/settings', 'POST', data);
        showSuccess('Map settings updated successfully');
    } catch (error) {
        console.error('Error updating map settings:', error);
        showError('Failed to update map settings');
    }
}
</script>

<style>
.map-preview {
    margin-top: 30px;
    padding: 20px;
    background: var(--bg-light);
    border-radius: 4px;
}

.map-preview h3 {
    margin-bottom: 15px;
}

#mapPreview {
    border: 1px solid var(--border-color);
    border-radius: 4px;
}

.input-group {
    display: flex;
}

.input-group .btn {
    margin-left: -1px;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.input-group .form-control {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}
</style>
