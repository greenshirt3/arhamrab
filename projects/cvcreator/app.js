const { createApp } = Vue;

createApp({
    data() {
        return {
            isAdmin: false,
            passwordInput: '',
            adminPassword: 'admin123', // CHANGE THIS PASSWORD!
            loginError: false,
            
            currentDesign: 'design-1',
            
            // The Resume Data Object
            cv: {
                name: 'Saif Ullah',
                title: 'Professional Printer & Designer',
                phone: '0300-1234567',
                email: 'arhamprinters@gmail.com',
                address: 'Domela Chowk, Jalalpur Jattan',
                summary: 'Experienced professional with over 18 years of disciplined service. Expert in large format printing and business management.',
                photo: null, // Base64 string
                skillsString: 'CorelDRAW, Large Format Printing, HTML/CSS, Team Leadership',
                skills: ['CorelDRAW', 'Large Format Printing', 'HTML/CSS', 'Team Leadership'],
                experience: [
                    {
                        position: 'Owner',
                        company: 'Arham Printers',
                        start: '2020',
                        end: 'Present',
                        desc: 'Managing large format printing operations, graphic design, and customer relations.'
                    }
                ],
                education: [
                    {
                        degree: 'Bachelor of Arts',
                        school: 'University of Punjab',
                        year: '2006'
                    }
                ]
            }
        }
    },
    methods: {
        checkPassword() {
            if(this.passwordInput === this.adminPassword) {
                this.isAdmin = true;
                this.loginError = false;
            } else {
                this.loginError = true;
            }
        },
        parseSkills() {
            this.cv.skills = this.cv.skillsString.split(',').map(s => s.trim()).filter(s => s);
        },
        handlePhotoUpload(e) {
            const file = e.target.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.cv.photo = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        addItem(type) {
            if(type === 'experience') {
                this.cv.experience.push({ position: '', company: '', start: '', end: '', desc: '' });
            } else if (type === 'education') {
                this.cv.education.push({ degree: '', school: '', year: '' });
            }
        },
        removeItem(type, index) {
            this.cv[type].splice(index, 1);
        },
        printCV() {
            window.print();
        },
        saveClientData() {
            const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(this.cv));
            const downloadAnchorNode = document.createElement('a');
            downloadAnchorNode.setAttribute("href", dataStr);
            downloadAnchorNode.setAttribute("download", this.cv.name + "_cv_data.json");
            document.body.appendChild(downloadAnchorNode);
            downloadAnchorNode.click();
            downloadAnchorNode.remove();
        },
        loadClientData(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const loadedData = JSON.parse(e.target.result);
                    this.cv = loadedData;
                } catch (error) {
                    alert("Invalid File Format");
                }
            };
            reader.readAsText(file);
        },
        resetData() {
            if(confirm("Clear all data?")) {
                this.cv = { name:'', title:'', skills:[], experience:[], education:[] }; // Simplified reset
                this.cv.skillsString = '';
            }
        }
    }
}).mount('#app');