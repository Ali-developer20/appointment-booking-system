<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite Appointment Systems</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom Glassmorphism Styles */
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glass-input {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        .glass-input:focus {
            outline: none;
            border-color: #a855f7; /* Purple focus */
            background: rgba(0, 0, 0, 0.4);
        }
        /* Custom Scrollbar for the list */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-black text-gray-200 font-sans p-6 selection:bg-purple-500 selection:text-white flex items-center justify-center">

    <div class="max-w-6xl w-full grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div class="glass-card rounded-2xl p-8 shadow-2xl">
            <h2 class="text-3xl font-bold mb-2 text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400">Book an Appointment</h2>
            <p class="text-gray-400 text-sm mb-6">Schedule your session using our secure system.</p>
            
            <form id="bookingForm" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Full Name</label>
                    <input type="text" id="name" required class="glass-input w-full px-4 py-3 rounded-lg transition-all" placeholder="ALi DeV">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Service Required</label>
                    <select id="service" required class="glass-input w-full px-4 py-3 rounded-lg transition-all appearance-none">
                        <option value="" class="text-gray-800">Select a service...</option>
                        <option value="Consultation" class="text-gray-800">Consultation</option>
                        <option value="Technical Support" class="text-gray-800">Technical Support</option>
                        <option value="Design Review" class="text-gray-800">Design Review</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Date</label>
                        <input type="date" id="date" required class="glass-input w-full px-4 py-3 rounded-lg transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Time</label>
                        <input type="time" id="time" required class="glass-input w-full px-4 py-3 rounded-lg transition-all">
                    </div>
                </div>
                
                <button type="submit" class="w-full mt-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-bold py-3 px-4 rounded-lg shadow-lg transform transition hover:-translate-y-1">
                    Confirm Booking
                </button>
                <div id="formMessage" class="hidden mt-4 text-center text-sm font-semibold rounded p-2"></div>
            </form>
        </div>

        <div class="glass-card rounded-2xl p-8 shadow-2xl flex flex-col h-[600px]">
            <h2 class="text-2xl font-bold mb-6 text-white border-b border-gray-600 pb-2">Upcoming Appointments</h2>
            
            <div id="appointmentsList" class="flex-1 overflow-y-auto space-y-4 pr-2">
                <p class="text-gray-400 animate-pulse text-center mt-10">Loading active bookings...</p>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetchBookings();

            // Handle Form Submission (POST)
            document.getElementById('bookingForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const payload = {
                    name: document.getElementById('name').value,
                    service: document.getElementById('service').value,
                    booking_date: document.getElementById('date').value,
                    booking_time: document.getElementById('time').value
                };

                const msgDiv = document.getElementById('formMessage');
                msgDiv.className = "mt-4 text-center text-sm font-semibold rounded p-2 text-yellow-300";
                msgDiv.innerText = "Processing...";
                msgDiv.classList.remove("hidden");

                // Send POST request to PHP API
                fetch('api/create_booking.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        msgDiv.className = "mt-4 text-center text-sm font-semibold rounded p-2 bg-green-500/20 text-green-300 border border-green-500/50";
                        msgDiv.innerText = data.message;
                        document.getElementById('bookingForm').reset();
                        fetchBookings(); // Refresh the list instantly
                    } else {
                        msgDiv.className = "mt-4 text-center text-sm font-semibold rounded p-2 bg-red-500/20 text-red-300 border border-red-500/50";
                        msgDiv.innerText = data.message;
                    }
                    setTimeout(() => msgDiv.classList.add("hidden"), 4000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    msgDiv.innerText = "System error occurred.";
                });
            });
        });

        // Handle Fetching Data (GET)
        function fetchBookings() {
            const listContainer = document.getElementById('appointmentsList');
            
            fetch('api/get_bookings.php')
                .then(response => response.json())
                .then(data => {
                    listContainer.innerHTML = ''; // Clear loading text
                    
                    if(data.status === 'success' && data.data.length > 0) {
                        data.data.forEach(booking => {
                            // Format date and time for better UI
                            const dateObj = new Date(booking.booking_date);
                            const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                            
                            // Build the UI card for each booking
                            const item = document.createElement('div');
                            item.className = "bg-white/5 border border-white/10 rounded-lg p-4 hover:bg-white/10 transition duration-300";
                            item.innerHTML = `
                                <div class="flex justify-between items-start mb-1">
                                    <h3 class="text-lg font-bold text-white">${booking.name}</h3>
                                    <span class="px-2 py-1 bg-purple-500/30 text-purple-300 text-xs rounded-full border border-purple-500/50">${booking.service}</span>
                                </div>
                                <div class="flex items-center text-gray-400 text-sm mt-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    ${formattedDate} at ${booking.booking_time}
                                </div>
                            `;
                            listContainer.appendChild(item);
                        });
                    } else {
                        listContainer.innerHTML = '<p class="text-gray-500 text-center mt-10">No appointments booked yet.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    listContainer.innerHTML = '<p class="text-red-400 text-center mt-10">Failed to load API data.</p>';
                });
        }
    </script>
</body>
</html>