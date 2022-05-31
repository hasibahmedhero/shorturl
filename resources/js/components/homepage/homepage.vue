<template>
	<div class="container" style="text-align:center;">
		<div class="home-box">
			<h2 class="text-center mb-4">Short URL Generator</h2>
			
			<form v-if="!result" @submit.prevent>
				<label class="control-label">Enter a long URL to make short URL:</label>
				<div class="input-group mt-2 mb-3">
					<div class="input-group-prepend">
						<span class="input-group-text" :class="{'input-error': userUrlInputError}"><img src="/favicon.ico"></span>
					</div>
					<input v-model="userUrlModel" @change="onUserUrlInputChange" @keyup="onUserUrlInputChange" type="text" class="form-control" :class="{'input-error': userUrlInputError}" placeholder="https://www.example.com">
				</div>
				<button @click="generateClick" class="btn btn-success" style="width:100%;">Make Short URL</button>
			</form>
			<div v-else class="result-container">
				<label class="mb-2">Your long URL:</label>
				<p style="">{{result.long_url}}</p>
				
				<label class="mb-2">Short URL:</label>
				<p class="result">
					<span>{{result.short_url}}</span>
					<span>
						<a :href="result.short_url" target="_blank">Visit</a>
						<span @click="copyClick">Copy</span>
					</span>
				</p>
				<button @click="anotherClick" class="btn btn-success" :class="{disabled:isLoading}">Short Another URL</button>
			</div>
			
			<p class="bottom-nav text-center mt-4"><router-link to="/contact">Contact</router-link> | <router-link to="/about">About</router-link> | <router-link to="/help">Help</router-link></p>
		</div>
		
		<div v-if="history.length" class="home-box history">
			<h5 class="mb-3">Recent</h5>
			<div v-for="item in history">
				<hr>
				<p class="mb-1">{{item.short_url}}</p>
				<p style="color:#198754">{{item.long_url}}</p>
			</div>
			<hr>
			<button @click="clearHistoryClick" class="btn btn-secondary">Clear Recent</button>
		</div>
		<p class="text-center my-5">© 2022 Hasib Ahmed</p>
		
	</div>
</template>

<script>
	export default {
		data() {
			return {
				userUrlModel: '',
				userUrlInputError: false,
				isLoading: false,
				result: null,
				history: JSON.parse(localStorage.getItem('history')) || []
			}
		},
		methods: {
			generateClick() {
				if (this.isLoading) return;
				if (!this.userUrlModel || this.userUrlModel == '') {
					this.$toastr.e('Please enter your long URL.');
					this.userUrlInputError = true;
					return;
				}
				
				this.isLoading = true;
				axios.post('/api/generate-url', {user_url:this.userUrlModel}).then((res) => {
					if (res.data.success) {
						this.result = res.data.data;
						this.saveHistory();
					} else {
						this.$toastr.e(res.data.error_message);
						if (res.data.error_message == 'Sorry! Inavlid URL') this.userUrlInputError = true;
					}
					this.isLoading = false;
					
				}).catch((e) => {
					this.$toastr.e('Something went wrong! Please try again later.');
					this.isLoading = false;
				});
			},
			onUserUrlInputChange() {
				this.userUrlInputError = false;
			},
			anotherClick() {
				this.result = null;
				this.userUrlModel = '';
			},
			copyClick() {
				var input = document.createElement('input');
				input.setAttribute('value', this.result.short_url);
				document.body.appendChild(input);
				input.select();
				var result = document.execCommand('copy');
				document.body.removeChild(input);
				this.$toastr.s('URL Copied!');
			},
			saveHistory() {
				this.history.unshift({
					short_url: this.result.short_url,
					long_url: this.result.long_url
				});
				this.history = this.history.slice(0, 10);
				localStorage.setItem('history', JSON.stringify(this.history));
			},
			clearHistoryClick() {
				if (confirm('Are you sure you want to clear recent history?') == true) {
					this.history = [];
					localStorage.setItem('history', JSON.stringify(this.history));
				}
			}
		}
	}
</script>
