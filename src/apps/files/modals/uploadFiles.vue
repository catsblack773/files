<template>
	<modal
		name="uploadFiles"
		:minWidth="minWidth"
		:minHeight="minHeight"
		:width="width"
		:height="height"
		:adaptive="true"
	>
		<header>
			<div class="header-wrapp">
				<div class="header-title">{{title}}</div>
				<div class="header-action-wrapp">
					<div class="header-close icon el-icon-close" @click="$modal.hide('uploadFiles')"></div>
				</div>
			</div>
		</header>
		<main class="main-wrapp">
			<el-form ref="form-create">
				<el-upload
					class="upload"
					ref="upload"
					:action="`${this.$store.getters.domans}/files/update-files/`"
					:headers="{'is-json': true}"
					:data="{path: path}"
					:auto-upload="true"
					:on-success="onSuccess"
					multiple
				>
					<div class="btn-wrapp">
						<el-button slot="trigger" size="small" type="primary">Дабавить файлы</el-button>
					</div>
				</el-upload>
			</el-form>
		</main>
	</modal>
</template>

<script>
export default {
	props: {
		title: {
			type: String,
			required: true
		},
		width: {
			type: Number,
			required: false,
			default: 300
		},
		height: {
			type: Number,
			required: false,
			default: 200
		},
		minWidth: {
			type: Number,
			required: false,
			default: 300
		},
		minHeight: {
			type: Number,
			required: false,
			default: 200
		},
		path: {
			type: String,
			required: true
		}
	},
	data() {
		return {
			fileList: [],
			limit: 10
		}
	},
	methods: {
		submitUpload () {
			this.$refs.upload.submit()
		},
		onSuccess (response) {
			if (response.res === true) {
				this.$message({
					message: response.message,
					type: 'success'
				})
				this.$emit('reLoad', {
					folders: response.folders,
					files: response.files,
					path: response.path
				})
			} else if (response.res === false) {
				this.$message.error(response.message)
			}
		}
	},
}
</script>

<style scoped>
	.upload {
		margin-top: 10px;
	}
	.upload .btn-wrapp {
		display: flex;
		align-items: center;
		justify-content: space-between;
	}
	.main-wrapp {
		padding: 0 15px;
	}
	.modal-content footer,
	.modal-content main,
	.modal-content header {
		width: 100%;
		font-size: 1rem;
		box-sizing: border-box;
	}
	.modal-content header {
		border-bottom: 1px solid #ccc;
	}
	.header-title {
		width: calc(100% - 80px);
		padding-left: 15px;
	}
	.header-action-wrapp {
		padding-right: 15px;
	}
	.modal-content footer {
		display: flex;
		align-items: center;
		height: 30px;
		border-top: 1px solid #ccc;
	}
	.header-wrapp {
		height: 30px;
		font-size: 14px;
	}
	.modal-content .information-panel,
	.modal-content .header-wrapp {
		display: flex;
		justify-content: space-between;
		align-items: center;
	}
	.modal-content .information-panel {
		padding: 0 14px;
		font-size: 13px;
	}
	.header-action-wrapp .icon {
		margin-right: 6px;
		font-weight: 900;
		cursor: pointer;
	}
	.header-action-wrapp .icon:last-child {
		margin-right: 0;
	}
	.header-action-wrapp .icon:hover {
		color: #555;
	}
	.modal-content .content {
		height: 100%;
	}
</style>