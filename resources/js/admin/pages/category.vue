<template>
  <div>
    <div class="content">
      <div class="container-fluid">
        <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
        <div
          class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20"
        >
          <p class="_title0">
            Categories
            <Button @click="addModal = true"
              ><Icon type="md-add" />Add category</Button
            >
          </p>
          <div class="_overflow _table_div">
            <table class="_table">
              <!-- TABLE TITLE -->
              <tr>
                <th>ID</th>
                <th>Tag name</th>
                <th>Created at</th>
                <th>Action</th>
              </tr>
              <!-- ITEMS -->
              <tr v-for="(tag, i) in tags" :key="i">
                <td>{{ tag.id }}</td>
                <td class="_table_name">
                  {{ tag.tagName }}
                </td>
                <td>{{ tag.created_at }}</td>
                <td>
                  <Button
                    type="info"
                    size="small"
                    @click="showEditModal(tag, i)"
                    >Edit</Button
                  >
                  <Button
                    type="error"
                    size="small"
                    @click="showDeletingModal(tag, i)"
                    :loading="tag.isDeleting"
                    >Delete</Button
                  >
                </td>
              </tr>
            </table>
          </div>
        </div>
        <!-- tag adding model -->
        <Modal
          v-model="addModal"
          title="Add category"
          :mask-closable="false"
          :closable="false"
        >
          <Input v-model="data.tagName" placeholder="Add category name" />
          <div class="space"></div>
          <Upload
            ref="uploads"
            type="drag"
            :headers="{
              'x-csrf-token': token,
              'X-Requested-With': 'XMLHttpRequest',
            }"
            :on-success="handleSuccess"
            :on-error="handleError"
            :on-format-error="handleFormatError"
            :max-size="2048"
            :on-exceeded-size="handleMaxSize"
            :before-upload="handleBeforeUpload"
            action="/app/upload"
          >
            <div style="padding: 20px 0">
              <Icon
                type="ios-cloud-upload"
                size="52"
                style="color: #3399ff"
              ></Icon>
              <p>Click or drag files here to upload</p>
            </div>
          </Upload>
          <div class="image_thumb" v-if="data.iconImage">
            <img :src="`/uploads/${data.iconImage}`" >
            <div class="demo_upload_list_cover">
            <Icon type="ios-trash-outline" @click="deleteImage"></Icon></div>
          </div>
          <div slot="footer">
            <Button type="default" @click="addModal = false">Close</Button>
            <Button
              type="primary"
              @click="addTag"
              :disabled="isAdding"
              :loading="isAdding"
              >{{ isAdding ? "Adding.." : "Add tag" }}</Button
            >
          </div>
        </Modal>
        <!-- Tag edit modal -->
        <Modal
          v-model="editModal"
          title="Edit tag"
          :mask-closable="false"
          :closable="false"
        >
          <Input v-model="editData.tagName" placeholder="Edit tag name" />
          <div slot="footer">
            <Button type="default" @click="editModal = false">Close</Button>
            <Button
              type="primary"
              @click="editTag"
              :disabled="isAdding"
              :loading="isAdding"
              >{{ isAdding ? "Editing.." : "Edit tag" }}</Button
            >
          </div>
        </Modal>
        <!-- Tag delete modal -->
        <Modal v-model="showDeleteModal" width="360">
          <p slot="header" style="color: #f60; text-align: center">
            <Icon type="ios-information-circle"></Icon>
            <span>Delete confirmation</span>
          </p>
          <div style="text-align: center">
            <p>Are you sure you want to delete this tag ?</p>
          </div>
          <div slot="footer">
            <Button
              type="error"
              size="large"
              long
              :loading="isDeleting"
              :disabled="isDeleting"
              @click="deleteTag"
              >Delete</Button
            >
          </div>
        </Modal>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      data: {
        iconimage: "",
        categoryName: "",
      },

      addModal: false,
      editModal: false,
      isAdding: false,
      tags: [],
      editData: {
        tagName: "",
      },
      index: -1,
      showDeleteModal: false,
      isDeleting: false,
      deleteItem: {},
      i: -1,
      deletingIndex: -1,
      token: "",
    };
  },
  methods: {
    async addTag() {
      if (this.data.tagName.trim() == "")
        return this.swr("Tag name is required");
      const res = await this.callApi("post", "app/create_tag", this.data);
      if (res.status === 201) {
        this.tags.unshift(res.data);
        this.s("Tag has been added successfully!");
        this.addModal = false;
        this.data.tagName = "";
      } else {
        if (res.data.errors.tagName) {
          this.i(res.data.errors.tagName[0]);
        } else {
          this.swr();
        }
      }
    },
    async editTag() {
      if (this.editData.tagName.trim() == "")
        return this.e("Tag name is required");
      const res = await this.callApi("post", "app/edit_tag", this.editData);
      if (res.status === 200) {
        this.tags[this.index].tagName = this.editData.tagName;
        this.tags.unshift(res.data);
        this.s("Tag has been edited successfully!");
        this.editModal = false;
      } else {
        if (res.data.errors.tagName) {
          this.i(res.data.errors.tagName[0]);
        } else {
          this.swr();
        }
      }
    },
    showEditModal(tag, index) {
      let obj = {
        id: tag.id,
        tagName: tag.tagName,
      };
      this.editData = obj;
      this.editModal = true;
      this.index = index;
    },
    async deleteTag() {
      this.isDeleting = true;
      const res = await this.callApi("post", "app/delete_tag", this.deleteItem);
      if (res.status === 200) {
        this.tags.splice(this.i, 1);
        this.s("Tag has been deleted successfully!");
      } else {
        this.swr();
      }
      this.isDeleting = false;
      this.showDeleteModal = false;
    },
    showDeletingModal(tag, i) {
      this.deleteItem = tag;
      this.i = i;
      this.showDeleteModal = true;
    },
    handleSuccess(res, file) {
      this.data.iconimage = res;
    },
    handleError(res, file) {
      console.log("res", res);
      console.log("file", file);
      this.$Notice.warning({
        title: "The file format is incorrect",
        desc: `${file.errors.file.length ? file.errors.file.length[0] : 'something went wrong!'}`
      });
    },
    handleFormatError(file) {
      this.$Notice.warning({
        title: "The file format is incorrect",
        desc:
          "File format of " +
          file.name +
          " is incorrect, please select jpg or png.",
      });
    },
    handleMaxSize(file) {
      this.$Notice.warning({
        title: "Exceeding file size limit",
        desc: "File  " + file.name + " is too large, no more than 2M.",
      });
    },
    async deleteImage() {
      let image = this.data.iconImage;
      this.data.iconImage = '';
      this.$refs.uploads.clearFiles()
      const res = await this.callApi('post', 'app/delete_image', {imageName: image})
      if (res.status != 200) {
        this.data.imageImage = image
        this.swr()
      }
    }
  },
  async created() {
    this.token = window.Laravel.csrfToken;
    const res = await this.callApi("get", "app/get_tag");
    if (res.status === 200) {
      this.tags = res.data;
    } else {
      this.swr();
    }
  },
};
</script>

<style>
</style>