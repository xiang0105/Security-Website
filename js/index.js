import {
  createApp
} from './vue.esm-browser.js';
const app = createApp({
  data() {
    return {
      data1: 0,
      data2: 0,
      about_id: 0,
      chart_id: 0,
      slide_id: 0,
      alert_msg: '',
      search_text: '',
      account: '',
      password: '',
      name: '',
      guest: '',
      email: '',
      question: '',
      phone: '',
      textarea: '',
      loginInfo: false,
      registerInfo: false,
      messages: [{
        text: '很高興能幫助您',
        who: ''
      }, ],
      about: [{
          title: '網站前言',
          content: '目前資訊爆炸時代顯示了「資訊流」的強迫擴散(媒體需產出報導或選擇捏造新聞)與「是非對錯、求證與否的新知」逐漸模糊。\n\n在這種掩護下，很多新型態網路詐騙的犯罪行為也順勢油然而生。在知識的鴻溝裡，人們開始試圖挖掘真相，甚至政府也大動作成立了事實查核網站來取締錯誤的網路訊息'
        },
        {
          title: '詐欺案分析',
          content: '隨著現代科技的進步造就社會繁榮，⺠眾⽣活品質也開始提升，尤其在網路技術⽇新⽉異的推波助瀾下，更是以無時差、無國界的形式傳播資訊。雖然網路讓⺠眾⽣活更加便利，卻也被有⼼⼈⼠利⽤成為犯罪途徑。\n\n近來發⽣中央研究院學術界⼈⼠及影星等重⼤詐騙案件，便是因為詐騙集團利⽤網路或其他途徑不法取得⺠眾個資後，利⽤假檢警及操作ATM解除分期付款等⽅式進⾏詐騙。此外也有藉由網路拍賣購物等模式騙取⺠眾財產，⺠眾因⽽在不知情的情況下受騙，詐騙⼿法可說是層出不窮。'
        },
        {
          title: '涉及法律',
          content: '本網站所提及之詐欺案件，主要係指犯下刑法第 339 條之刑事案件，其所列條⽂如下：「意圖為⾃⼰或第三⼈不法之所有，以詐術使⼈ 將本⼈或第三⼈之物交付者，……。」\n\n換⾔之，當犯罪者本⾝主觀上有想要騙取被害⼈財物之意思，並使⽤詐術讓被害⼈誤以為是真的情形下將財物交給犯罪者，最後使被害⼈因⽽受到損害，如此便構成詐欺案件。'
        },
      ],
      chart: [
        './img/301.png',
        './img/302.png',
        './img/303.png',
        './img/304.png',
        './img/305.png',
        './img/306.png',
      ],
      slide: [{
          title: '郵件的防護',
          english: 'Protection',
          items: ['阻斷惡意郵件', '檢視郵件內容', '抵制不明郵件', '提升郵件安全', ],
          btn: '搶先體驗',
          color: 'text-danger',
          icon: 'fa-envelope',
          class: 'btn-outline-danger'
        },
        {
          title: '軟體安全應用',
          english: 'Security',
          items: ['守護應用程式', '阻斷惡意行為', '定期偵測來電', '提供安全報表', ],
          btn: '會員登入',
          color: 'text-success',
          icon: 'fa-cloud',
          class: 'btn-outline-success'
        },
        {
          title: '雲端防火牆',
          english: 'Firewall',
          items: ['阻斷惡意封包', '提升安全性能', '檢測網路環境', '網路警察合作', ],
          btn: '聯絡我們',
          color: 'text-info',
          icon: 'fa-fire',
          class: 'btn-outline-info'
        },
      ],
      search_list: [{
          title: '關於我們',
          content: '關於我們在本網站擔當介紹網頁和我們的理念，讓民眾可以更簡單的了解我們',
          position: '#about'
        },
        {
          title: '資安健檢',
          content: '資安健檢是我們團隊去整理龐大的資料作成圖表給民眾觀看，讓您可以立刻切入主題',
          position: '#information'
        },
        {
          title: '服務資訊',
          content: '服務資訊是我們所推薦的產品，全部都是以消費者的角度去著想，讓您使用的安心',
          position: '#services'
        },
        {
          title: '聯絡我們',
          content: '聯絡我們是給民眾一個可以留言的專欄，讓您可以提供您心中的疑問讓我們可以更加完善',
          position: '#contact'
        },
        {
          title: '郵件的防護',
          content: '郵件的保護是專門為了郵件所設計的商品，獨家的安全技術，讓您安心傳訊息',
          position: '#services',
          action(_this) {
            _this.slide_id = 0
          }
        },
        {
          title: '軟體安全應用',
          content: '軟體安全應用是為了應用程式所設計的產品，幫您把關每一個進入的封包，隔絕99%的危害',
          position: '#services',
          action(_this) {
            _this.slide_id = 1
          }
        },
        {
          title: '雲端防火牆',
          content: '雲端防火牆是仿造防火牆的運作原理的一項產品，不僅保護您的內在網路，更可以和獨家的網路警察合作。',
          position: '#services',
          action(_this) {
            _this.slide_id = 2
          }
        },
      ],
    }
  },
  methods: {
    $$(...data) {
      return $(...data);
    },
    alert(msg) {
      this.alert_msg = msg;
      $('#alert').modal()
    },
    alert2() {
      this.alert_msg = `感謝${this.guest}先生/小姐的回復\n我們會將你所問的${this.question}和內容${this.textarea}作整理\n請這三天留意您的聯絡電話${this.phone}或電子信箱${this.email}`;
      $('#alert').modal();
      $('#form').reset();
    },
    search(item) {
      location.href = item.position;
      $('#search').modal('hide');
      this.search_text = '';
      item.action(this);
    },
    turn() {
      $('#turn').addClass('turn');
      setTimeout(() => {
        $('#turn').removeClass('turn')
      }, 1000)
    },
    toggle(i) {
      if (i == 0) {
        this.alert_msg = '您的試用期為三個月';
        $('#alert').modal()
      } else if (i == 1) {
        $('#login').modal()
      } else {
        location.href = '#contact'
      }
    },
    login() {
      $.ajax({
        url: 'login.php',
        data: {
          account: this.account,
          password: this.password,
        }
      }).then(data => {
        this.loginInfo = {
          insended: true,
          name: data
        };
        this.account = '';
        this.password = '';
      })
    },
    register() {
      $.ajax({
        url: 'register.php',
        data: {
          account: this.account,
          password: this.password,
          name: this.name,
        }
      }).then(message => {
        this.registerInfo = {
          insended: true,
          message
        };
        this.account = '';
        this.password = '';
        this.name = '';
      })
    },
    submit() {
      const text = $('#text').val();
      $('#text').val(' ');

      if (!text) return;

      const a = [
        '請稍後喔',
        '很快會有專人為您服務',
        '感謝您的留言',
      ];

      if (text == '關於我們') {
        this.messages.push({
          text: '正在前往關於我們'
        });
        setTimeout(() => {
          location.href = '#about'
        }, 500)
      } else if (text == '資安健檢') {
        this.messages.push({
          text: '正在前往資安健檢'
        });
        setTimeout(() => {
          location.href = '#information'
        }, 500)
      } else if (text == '會員註冊') {
        this.messages.push({
          text: '正在前往會員註冊'
        });
        setTimeout(() => {
          $('#register').modal();
        }, 500)
      } else if (text == '聯絡我們') {
        this.messages.push({
          text: '正在前往聯絡我們'
        });
        setTimeout(() => {
          location.href = '#contact'
        }, 500)
      } else {
        this.messages.push({
          text: text,
          who: 'guest'
        });
        this.messages.push({
          text: a[Math.floor(Math.random() * a.length)]
        });
      }
    }

  },
  watch: {
    messages: {
      handler() {
        setTimeout(() => {
          $('#robot .card-body').scrollTop($('#bottom').offset().top)
        })
      },
      deep: true
    }
  },
  computed: {
    filter() {
      return this.search_text.split(' ').reduce((list, text) => {
        return list.filter(item => {
          return item.content.match(new RegExp(text, 'i'));
        });
      }, this.search_list)
    }
  },
  created() {
    setTimeout((interval) => {
      clearInterval(interval);
      this.data1 = 4902;
      this.data2 = 10091;
    }, 2000, setInterval(() => {
      this.data1 += 120;
      this.data2 += 320;
    },50))
  }
});
app.mount('#app');

const a = new IntersectionObserver(j => {
  j.forEach(i => {
    if (i.isIntersecting) {
      $(i.target).addClass('show')
    } else {
      $(i.target).removeClass('show')
    }
  })
})

$('.hidden').get().forEach(el => {
  a.observe(el)
})
$('.hidden2').get().forEach(el => {
  a.observe(el)
})
$('.hidden3').get().forEach(el => {
  a.observe(el)
})
