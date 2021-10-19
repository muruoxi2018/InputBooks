Component({
  options: {
    addGlobalClass: true,
  },
  data: {
    isbn: '请开始录入',
    biaoshi: '测试人员',
    tips:'请填写标识符'
  },
  methods: {

    bindinput(e) {
      this.setData({
        biaoshi: e.detail.value
      })
    },
    saoma() {
      let that = this
      if (that.data.biaoshi == '测试人员') {
        this.setData({
          tips:'请填写标识符',
          loadModal: true
        })
        setTimeout(() => {
          this.setData({
            loadModal: false
          })
        }, 2000)
      } else {
        wx.scanCode({
          onlyFromCamera: true,
          success(res) {
            that.setData({
              loadModal: true,
              tips:'请稍等'
            })
            wx.request({
              url: 'https://books.muruoxi.com/api.php',
              data: {
                isbn: res.result,
                biaoshi: that.data.biaoshi
              },
              header: {
                'content-type': 'application/json' // 默认值
              },
              success(res) {
                that.setData({
                  loadModal: false
                })
                if (res.data.code == 'success') {
                  that.setData({
                    isbn: res.data.message.ISBN,
                    title: res.data.message.title,
                    author: res.data.message.author,
                    year: res.data.message.year,
                    press: res.data.message.press,
                    page: res.data.message.page,
                    money: res.data.message.money,
                    binding: res.data.message.binding,
                    
                  })
                }
              },
              fail(res) {
                that.setData({
                  tips:res.data
                })
                setTimeout(() => {
                  this.setData({
                    loadModal: false
                  })
                }, 2000)
              }
            })
          }
        })
      }

    },
    onShareAppMessage() {
      return {
        title: '给学校写的一个快速录入图书的小程序',
        path: 'pages/about/home/home'
      }
    }
  }
})