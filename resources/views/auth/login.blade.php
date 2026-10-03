<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script>
      tailwind = { config: { corePlugins: { preflight: false } } };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter%3Aital%2Cwght%400%2C100..900%3B1%2C100..900&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
  </head>
  <body style="background-color: #F4F5F7">
    <div
      data-pencil-name="SmartSchool - Login Admin Sekolah"
      class="box-border w-[1440px] h-[900px] bg-[#F4F5F7] overflow-hidden relative"
    >
      <div
        data-pencil-name="Login Split"
        class="box-border w-[1440px] h-[900px] absolute left-0 top-0 flex flex-row gap-0 justify-start items-start bg-[#FFFFFF] [z-index:0]"
      >
        <div
          data-pencil-name="Branding Panel"
          class="box-border w-[600px] shrink-0 h-full flex flex-col gap-[24px] p-[48px] justify-between items-start bg-[#0F1E33]"
        >
          <div
            data-pencil-name="Logo Row"
            class="box-border w-full h-fit shrink-0 flex flex-row gap-[12px] justify-start items-center"
          >
            <div
              data-pencil-name="Logo Mark"
              class="box-border w-[44px] shrink-0 h-[44px] flex flex-row gap-0 justify-center items-center bg-[#2563EB] rounded-[12px]"
            >
              <div
                data-pencil-name="Logo Letter"
                class="text-[22px]/[normal] box-border text-[#FFFFFF] font-[Inter,system-ui,sans-serif] font-bold text-left [white-space:nowrap]"
              >
                S
              </div>
            </div>
            <div
              data-pencil-name="Logo Text"
              class="box-border w-fit shrink-0 h-fit flex flex-col gap-[2px] justify-start items-start"
            >
              <div
                data-pencil-name="Product"
                class="text-[18px]/[normal] box-border text-[#FFFFFF] font-[Inter,system-ui,sans-serif] font-bold tracking-[1px] text-left [white-space:nowrap]"
              >
                Product SmartSchool ERP
              </div>
              <div
                data-pencil-name="Sub"
                class="text-[12px]/[normal] box-border text-[#94A3B8] font-[Inter,system-ui,sans-serif] font-normal text-left [white-space:nowrap]"
              >
                Digitalisasi Tata Kelola &amp; Pembayaran Terpadu
              </div>
            </div>
          </div>
          <div
            data-pencil-name="Branding Middle"
            class="box-border w-full h-fit shrink-0 flex flex-col gap-[20px] justify-start items-start"
          >
            <div
              data-pencil-name="Badge"
              class="text-[11px]/[normal] box-border text-[#60A5FA] font-[Inter,system-ui,sans-serif] font-semibold tracking-[1px] text-left [white-space:nowrap]"
            >
              â— PANEL ADMIN v2.4
            </div>
            <div
              data-pencil-name="Headline"
              class="text-[36px]/[41px] box-border w-full text-[#FFFFFF] font-[Inter,system-ui,sans-serif] font-bold text-left"
            >
              Kelola Sekolah &amp; Pembayaran dalam Satu Dasbor
            </div>
            <div
              data-pencil-name="Desc"
              class="text-[14px]/[21px] box-border w-full text-[#94A3B8] font-[Inter,system-ui,sans-serif] font-normal text-left"
            >
              Pantau siswa, guru, jadwal pelajaran, tagihan, dan laporan sekolah secara real-time
              dengan keamanan tingkat enterprise.
            </div>
            <div
              data-pencil-name="Stats Card"
              class="box-border w-full h-fit shrink-0 flex flex-col gap-[16px] p-[20px] justify-start items-start bg-[#162C4E] [outline:1px_solid_#243B5E] [outline-offset:-0.5px] rounded-[16px]"
            >
              <div
                data-pencil-name="Stats Header"
                class="box-border w-full h-fit shrink-0 flex flex-row gap-[12px] justify-between items-center"
              >
                <div
                  data-pencil-name="T1"
                  class="text-[13px]/[normal] box-border text-[#FFFFFF] font-[Inter,system-ui,sans-serif] font-semibold text-left [white-space:nowrap]"
                >
                  Aktivitas Semester Ganjil
                </div>
                <div
                  data-pencil-name="T2"
                  class="text-[12px]/[normal] box-border text-[#22C55E] font-[Inter,system-ui,sans-serif] font-semibold text-left [white-space:nowrap]"
                >
                  +12,4% â–²
                </div>
              </div>
              <div
                data-pencil-name="Bar Chart"
                class="box-border w-full h-[96px] shrink-0 flex flex-row gap-[10px] justify-between items-end"
              >
                <div
                  data-pencil-name="Bar"
                  class="box-border [flex:1_1_0] h-[42px] bg-[#2D4A71] rounded-[6px]"
                ></div>
                <div
                  data-pencil-name="Bar"
                  class="box-border [flex:1_1_0] h-[68px] bg-[#2D4A71] rounded-[6px]"
                ></div>
                <div
                  data-pencil-name="Bar"
                  class="box-border [flex:1_1_0] h-[52px] bg-[#2D4A71] rounded-[6px]"
                ></div>
                <div
                  data-pencil-name="Bar"
                  class="box-border [flex:1_1_0] h-[84px] bg-[#2563EB] rounded-[6px]"
                ></div>
                <div
                  data-pencil-name="Bar"
                  class="box-border [flex:1_1_0] h-[61px] bg-[#2D4A71] rounded-[6px]"
                ></div>
                <div
                  data-pencil-name="Bar"
                  class="box-border [flex:1_1_0] h-[96px] bg-[#2563EB] rounded-[6px]"
                ></div>
                <div
                  data-pencil-name="Bar"
                  class="box-border [flex:1_1_0] h-[74px] bg-[#2D4A71] rounded-[6px]"
                ></div>
                <div
                  data-pencil-name="Bar"
                  class="box-border [flex:1_1_0] h-[58px] bg-[#2D4A71] rounded-[6px]"
                ></div>
                <div
                  data-pencil-name="Bar"
                  class="box-border [flex:1_1_0] h-[88px] bg-[#2563EB] rounded-[6px]"
                ></div>
                <div
                  data-pencil-name="Bar"
                  class="box-border [flex:1_1_0] h-[70px] bg-[#2D4A71] rounded-[6px]"
                ></div>
                <div
                  data-pencil-name="Bar"
                  class="box-border [flex:1_1_0] h-[50px] bg-[#2D4A71] rounded-[6px]"
                ></div>
                <div
                  data-pencil-name="Bar"
                  class="box-border [flex:1_1_0] h-[78px] bg-[#2D4A71] rounded-[6px]"
                ></div>
              </div>
              <div
                data-pencil-name="Mini Stats"
                class="box-border w-full h-fit shrink-0 flex flex-row gap-[12px] justify-start items-start"
              >
                <div
                  data-pencil-name="Siswa"
                  class="box-border [flex:1_1_0] h-fit flex flex-col gap-[2px] p-[12px] justify-start items-start bg-[#0F1E33] rounded-[12px]"
                >
                  <div
                    data-pencil-name="Siswa val"
                    class="text-[18px]/[normal] box-border text-[#FFFFFF] font-[Inter,system-ui,sans-serif] font-bold text-left [white-space:nowrap]"
                  >
                    1.284
                  </div>
                  <div
                    data-pencil-name="Siswa lab"
                    class="text-[11px]/[normal] box-border text-[#94A3B8] font-[Inter,system-ui,sans-serif] font-normal text-left [white-space:nowrap]"
                  >
                    Siswa
                  </div>
                </div>
                <div
                  data-pencil-name="Dosen"
                  class="box-border [flex:1_1_0] h-fit flex flex-col gap-[2px] p-[12px] justify-start items-start bg-[#0F1E33] rounded-[12px]"
                >
                  <div
                    data-pencil-name="Guru val"
                    class="text-[18px]/[normal] box-border text-[#FFFFFF] font-[Inter,system-ui,sans-serif] font-bold text-left [white-space:nowrap]"
                  >
                    87
                  </div>
                  <div
                    data-pencil-name="Guru lab"
                    class="text-[11px]/[normal] box-border text-[#94A3B8] font-[Inter,system-ui,sans-serif] font-normal text-left [white-space:nowrap]"
                  >
                    Guru
                  </div>
                </div>
                <div
                  data-pencil-name="Prodi"
                  class="box-border [flex:1_1_0] h-fit flex flex-col gap-[2px] p-[12px] justify-start items-start bg-[#0F1E33] rounded-[12px]"
                >
                  <div
                    data-pencil-name="Kelas val"
                    class="text-[18px]/[normal] box-border text-[#FFFFFF] font-[Inter,system-ui,sans-serif] font-bold text-left [white-space:nowrap]"
                  >
                    36
                  </div>
                  <div
                    data-pencil-name="Kelas lab"
                    class="text-[11px]/[normal] box-border text-[#94A3B8] font-[Inter,system-ui,sans-serif] font-normal text-left [white-space:nowrap]"
                  >
                    Kelas
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            data-pencil-name="Branding Footer"
            class="box-border w-full h-fit shrink-0 flex flex-col gap-[12px] justify-start items-start"
          >
            <div
              data-pencil-name="Quote"
              class="text-[12px]/[normal] box-border w-full text-[#CBD5E1] font-[Inter,system-ui,sans-serif] font-normal italic text-left"
            >
              â€œSejak pakai SmartSchool, rekap akademik dan pembayaran 3x lebih cepat.â€ â€” Dr. Ratna,
              Kepala Sekolah
            </div>
            <div
              data-pencil-name="Copy"
              class="text-[11px]/[normal] box-border text-[#5B6B84] font-[Inter,system-ui,sans-serif] font-normal text-left [white-space:nowrap]"
            >
              Â© 2026 SMA Nusantara â€¢ ISO 27001 â€¢ v2.4.1
            </div>
          </div>
        </div>
        <div
          data-pencil-name="Login Panel"
          class="box-border [flex:1_1_0] h-full flex flex-col gap-[16px] p-[32px_64px] justify-start items-center bg-[#FFFFFF]"
        >
          <div
            data-pencil-name="Top Bar"
            class="box-border w-full h-fit shrink-0 flex flex-row gap-[12px] justify-between items-center"
          >
            <div
              data-pencil-name="LogoMini"
              class="text-[12px]/[normal] box-border text-[#0F172A] font-[Inter,system-ui,sans-serif] font-bold tracking-[0.5px] text-left [white-space:nowrap]"
            >
              SmartSchool â€¢ Admin
            </div>
            <div
              data-pencil-name="Lang Group"
              class="box-border w-fit shrink-0 h-fit flex flex-row gap-[8px] justify-start items-center"
            >
              <svg
                data-pencil-name="Globe"
                data-icon-name="globe"
                data-icon-set="lucide"
                viewBox="0 0 13.99993896484375 14"
                preserveAspectRatio="xMidYMid meet"
                xmlns="http://www.w3.org/2000/svg"
                class="box-border w-[16px] shrink-0 h-[16px]"
              >
                <path
                  d="M6.76074 0.58789q-1.8457 0.09912-3.3291 1.09375-1.0083 0.66992-1.70215 1.65772-0.69385 0.98779-0.97412 2.16357-0.16748 0.71436-0.16748 1.49707 0 0.61523 0.0957 1.16895 0.09912 0.55371 0.32471 1.12793 0.4751 1.24414 1.43897 2.21826 0.96729 0.97412 2.21484 1.46289 0.58789 0.22559 1.1416 0.33154 0.55371 0.10254 1.19629 0.10254 0.75537 0 1.43213-0.15381 0.68018-0.15381 1.35351-0.4751 1.26123-0.61865 2.16358-1.70215 0.90234-1.0835 1.25097-2.43359 0.35205-1.35352 0.11963-2.72412-0.229-1.37402-1.01513-2.52246-0.81006-1.18945-2.05078-1.92432-1.2373-0.73486-2.66602-0.86133-0.48877-0.04102-0.82715-0.02734z m-1.14844 1.3877q-1.09033 1.62354-1.39794 3.52734-0.04102 0.16748-0.07178 0.44092-0.02734 0.27344-0.02735 0.34179l0 0.02735q0 0.07178-0.03418 0.08545-0.03418 0.01367-0.21875 0.02734l-2.08496 0 0.01368-0.09912q0.08545-0.64258 0.34179-1.28516 0.25977-0.646 0.65283-1.16211 1.0083-1.36035 2.64551-1.89013l0.19824-0.05811q0.01367 0-0.01709 0.04444z m3.25049 0.12304q1.03564 0.37939 1.84571 1.19287 0.81348 0.81006 1.20654 1.84571 0.22217 0.60156 0.29394 1.18945l0.01368 0.09912-2.08838 0q-0.18115-0.01367-0.21533-0.02734-0.03418-0.01367-0.03418-0.08545l0-0.02735q0-0.06836-0.03077-0.34179-0.02734-0.27344-0.06836-0.45459-0.30762-1.89014-1.38427-3.51367l-0.04444-0.04444 0.15381 0.04444q0.14014 0.04102 0.35205 0.12304z m-1.72265 0.14014q0.64258 0.82715 1.04931 1.85596 0.40674 1.02881 0.50586 2.02343l0.02735 0.30762-3.45899 0 0.04102-0.29394q0.08545-0.90918 0.42041-1.83204 0.33838-0.92627 0.88183-1.70898 0.11279-0.16748 0.2461-0.34863 0.1333-0.18457 0.14697-0.17774 0.01367 0.00684 0.14014 0.17432z m-3.03858 5.36279q0.01367 0.02734 0.01367 0.10596 0 0.0752 0.02735 0.34863 0.03076 0.27344 0.07178 0.44092 0.30762 1.90381 1.38427 3.52734l0.04444 0.04444-0.15381-0.04444q-1.66797-0.50244-2.70362-1.90381-0.39307-0.51611-0.65283-1.15869-0.25635-0.646-0.34179-1.28857l-0.01368-0.09912 2.1294 0.01367q0.18115 0 0.19482 0.01367z m4.6211 0.02735q-0.01367 0.05811-0.02735 0.23925-0.07178 0.75537-0.33154 1.55518-0.25635 0.79639-0.64941 1.4834-0.15381 0.2666-0.42041 0.64599-0.2666 0.37598-0.29395 0.37598-0.02734 0-0.29395-0.37598-0.2666-0.37939-0.42041-0.64599-0.39307-0.68701-0.65283-1.4834-0.25635-0.7998-0.32812-1.55518l-0.04102-0.29394 3.45899 0 0 0.05469z m3.48632 0.04443q-0.16748 1.32959-0.98779 2.44385-0.82031 1.11084-2.05078 1.67138-0.23926 0.09912-0.52637 0.19825-0.28711 0.0957-0.27343 0.06836l0.07177-0.09913q0.42041-0.64258 0.76221-1.45605 0.3418-0.81348 0.5127-1.62354 0.06836-0.28027 0.11621-0.64941 0.05127-0.37256 0.05127-0.51269l0-0.02735q0-0.07178 0.03418-0.08545 0.03418-0.01367 0.21533-0.02734l2.08838 0-0.01368 0.09912z"
                  fill="#64748B"
                ></path>
              </svg>
              <div
                data-pencil-name="Lang"
                class="text-[13px]/[normal] box-border text-[#64748B] font-[Inter,system-ui,sans-serif] font-normal text-left [white-space:nowrap]"
              >
                Bahasa Indonesia â–¾
              </div>
              <div
                data-pencil-name="Help"
                class="text-[13px]/[normal] box-border text-[#64748B] font-[Inter,system-ui,sans-serif] font-normal text-left [white-space:nowrap]"
              >
                Bantuan
              </div>
            </div>
          </div>
          <div
            data-pencil-name="Center"
            class="box-border w-full [flex:1_1_0] flex flex-col gap-0 justify-center items-center"
          >
            <div
              data-pencil-name="Login Card"
              class="box-border w-[440px] h-fit shrink-0 flex flex-col gap-[14px] justify-start items-start"
            >
              <div
                data-pencil-name="Mobile Badge"
                class="text-[11px]/[normal] box-border text-[#2563EB] font-[Inter,system-ui,sans-serif] font-bold tracking-[1px] text-left [white-space:nowrap]"
              >
                ADMINISTRATOR
              </div>
              <div
                data-pencil-name="Title"
                class="text-[28px]/[normal] box-border w-full text-[#0F172A] font-[Inter,system-ui,sans-serif] font-bold text-left"
              >
                Selamat Datang Kembali
              </div>
              <div
                data-pencil-name="Subtitle"
                class="text-[14px]/[normal] box-border w-full text-[#64748B] font-[Inter,system-ui,sans-serif] font-normal text-left"
              >
                Masuk ke panel admin untuk mengelola data sekolah Anda.
              </div>
              <div
                data-pencil-name="Email Institusi"
                class="box-border w-full h-fit shrink-0 flex flex-col gap-[8px] justify-start items-start"
              >
                <div
                  data-pencil-name="Email Institusi label"
                  class="text-[13px]/[normal] box-border text-[#0F172A] font-[Inter,system-ui,sans-serif] font-semibold text-left [white-space:nowrap]"
                >
                  Email Sekolah
                </div>
                <div
                  data-pencil-name="Email Institusi input"
                  class="box-border w-full h-fit shrink-0 flex flex-row gap-[10px] p-[12px_14px] justify-start items-center bg-[#FFFFFF] [outline:1px_solid_#E2E8F0] [outline-offset:-0.5px] rounded-[10px]"
                >
                  <svg
                    data-pencil-name="mail"
                    data-icon-name="mail"
                    data-icon-set="lucide"
                    viewBox="0 0 13.99993896484375 14"
                    preserveAspectRatio="xMidYMid meet"
                    xmlns="http://www.w3.org/2000/svg"
                    class="box-border w-[18px] shrink-0 h-[18px]"
                  >
                    <path
                      d="M2.05762 1.77734q-0.28027 0.04102-0.53321 0.16748-0.34863 0.19824-0.58789 0.50586-0.23584 0.30762-0.32129 0.70069-0.01367 0.10938-0.02734 0.66992l0 3.17871 0 3.17871q0.01367 0.56055 0.02734 0.66992 0.11279 0.5332 0.50245 0.90577 0.39307 0.36914 0.92627 0.46826 0.16748 0.02734 4.95605 0.02734 4.78857 0 4.95605-0.02734 0.5332-0.09912 0.92286-0.46826 0.39307-0.37256 0.50586-0.90577 0.01367-0.10938 0.02734-0.66992l0-3.17871 0-3.17871q-0.01367-0.56055-0.02734-0.66992-0.12647-0.54688-0.53321-0.92285-0.40674-0.37939-0.95019-0.46485-0.1709-0.01367-4.93897-0.01367-4.76465 0-4.90478 0.02734z m9.81298 1.17579q0.09912 0.04443 0.18116 0.12304 0.08545 0.0752 0.12646 0.16065 0.04443 0.08203 0.05127 0.12988 0.00684 0.04785 0.00684 0.18799l0 0.21191-2.46436 1.58252q-2.46436 1.56543-2.53613 1.60303-0.06836 0.03418-0.16748 0.03418l-0.02734 0.01367q-0.0957 0.01367-0.2085-0.04102-0.15381-0.07178-0.65967-0.37939l-4.40918-2.81299 0-0.21191q0-0.14014 0.00684-0.18799 0.00684-0.04785 0.04101-0.12305 0.0376-0.07861 0.12647-0.16064 0.09229-0.08545 0.16748-0.1128 0.07861-0.03076 0.70752-0.04443l4.18701 0 4.18701 0q0.61523 0 0.68359 0.02735z m-5.63964 5.02783q0.22217 0.10938 0.46142 0.15381 0.09912 0.02734 0.30762 0.02734 0.2085 0 0.30762-0.02734 0.30762-0.05811 0.54687-0.19825 0.14014-0.08203 2.26612-1.42529l2.12939-1.36035-0.01367 5.50293-0.04102 0.09912q-0.11279 0.2085-0.3247 0.29395l-0.09571 0.02734-9.5498 0-0.09571-0.02734q-0.21191-0.08545-0.3247-0.29395l-0.04102-0.09912-0.01367-5.50293 2.17041 1.37402q2.17041 1.38428 2.31055 1.45606z"
                      fill="#94A3B8"
                    ></path>
                  </svg>
                  <input
                    type="email"
                    name="email"
                    placeholder="admin@smanusantara.sch.id"
                    autocomplete="username"
                    class="login-input text-[14px]/[normal] box-border text-[#0F172A] font-[Inter,system-ui,sans-serif] font-normal text-left placeholder:text-[#94A3B8]"
                  />
                </div>
              </div>
              <div
                data-pencil-name="Kata Sandi"
                class="box-border w-full h-fit shrink-0 flex flex-col gap-[8px] justify-start items-start"
              >
                <div
                  data-pencil-name="Kata Sandi label"
                  class="text-[13px]/[normal] box-border text-[#0F172A] font-[Inter,system-ui,sans-serif] font-semibold text-left [white-space:nowrap]"
                >
                  Kata Sandi
                </div>
                <div
                  data-pencil-name="Kata Sandi input"
                  class="box-border w-full h-fit shrink-0 flex flex-row gap-[10px] p-[12px_14px] justify-start items-center bg-[#FFFFFF] [outline:1px_solid_#E2E8F0] [outline-offset:-0.5px] rounded-[10px]"
                >
                  <svg
                    data-pencil-name="lock"
                    data-icon-name="lock"
                    data-icon-set="lucide"
                    viewBox="0 0 13.99993896484375 14"
                    preserveAspectRatio="xMidYMid meet"
                    xmlns="http://www.w3.org/2000/svg"
                    class="box-border w-[18px] shrink-0 h-[18px]"
                  >
                    <path
                      d="M6.77441 0.60156q-0.72803 0.02734-1.38769 0.3794-0.65625 0.34863-1.12451 0.92285-0.46826 0.57422-0.64942 1.28857l0 0.01367q-0.07178 0.29395-0.08545 0.48536-0.01367 0.18799-0.02734 1.02539l0 1.10742-0.2666 0q-0.40674 0-0.63916 0.04443-0.229 0.04102-0.45117 0.15039-0.32471 0.16748-0.56397 0.44776-0.23584 0.28027-0.34863 0.64599l-0.05469 0.15381 0 4.7168 0.05469 0.15381q0.15723 0.51953 0.56055 0.86132 0.40674 0.3418 0.93994 0.39991 0.14014 0.02734 4.26904 0.02734 4.12891 0 4.26904-0.02734 0.30762-0.02734 0.58789-0.16748 0.32471-0.15381 0.56055-0.44092 0.23926-0.28711 0.35205-0.65283l0.05469-0.15381 0-4.7168-0.05469-0.15381q-0.11279-0.36572-0.35205-0.64599-0.23584-0.28027-0.56055-0.44776-0.22217-0.10938-0.45459-0.15039-0.229-0.04443-0.63574-0.04443l-0.2666 0 0-1.13477q-0.01367-0.81006-0.02734-0.99804-0.01367-0.19141-0.08545-0.47168l0-0.02735q-0.18115-0.71436-0.66651-1.30224-0.48193-0.58789-1.15185-0.93653-0.82715-0.40674-1.79444-0.35205z m0.70069 1.20313q0.75537 0.15381 1.2749 0.7417 0.3623 0.40674 0.50244 0.92627 0.05811 0.19482 0.06494 0.41357 0.00684 0.21533 0.00684 0.9707l0 0.96729-4.64844 0 0-1.06299q0-0.82715 0.01367-1.0083 0.01367-0.18457 0.09912-0.40674 0.22217-0.64258 0.74854-1.06982 0.52637-0.42725 1.19629-0.5127 0.14014-0.01367 0.3623 0 0.22559 0.01367 0.3794 0.04102z m3.85205 5.25q0.08203 0.04443 0.15723 0.12304 0.07861 0.0752 0.11279 0.15381 0.0376 0.0752 0.0581 0.19483 0.02051 0.11621 0.00684 2.19092l0 1.6372q0 0.3623-0.02734 0.48877-0.01367 0.08545-0.07178 0.14014l-0.01367 0.02734q-0.11279 0.12646-0.26661 0.19824l-0.0957 0.04102-8.37402 0-0.0957-0.04102q-0.15381-0.0581-0.26661-0.19824l-0.01367-0.02734q-0.05811-0.06836-0.07178-0.14014-0.02734-0.12646-0.02734-0.48877l0-1.65088q-0.01367-2.08838 0.01367-2.20117 0.01367-0.18115 0.1333-0.31445 0.11963-0.1333 0.28711-0.17432 0.06836-0.01367 4.25537-0.01367l4.20069 0.01367 0.09912 0.04102z"
                      fill="#94A3B8"
                    ></path>
                  </svg>
                  <input
                    type="password"
                    name="password"
                    placeholder="••••••••••"
                    autocomplete="current-password"
                    class="login-input text-[14px]/[normal] box-border text-[#0F172A] font-[Inter,system-ui,sans-serif] font-normal text-left placeholder:text-[#0F172A]"
                  />
                  
                  <svg
                    data-pencil-name="Eye"
                    data-icon-name="eye"
                    data-icon-set="lucide"
                    viewBox="0 0 13.99993896484375 14"
                    preserveAspectRatio="xMidYMid meet"
                    xmlns="http://www.w3.org/2000/svg"
                    class="box-border w-[18px] shrink-0 h-[18px]"
                  >
                    <path
                      d="M6.67871 2.35156q-1.23389 0.04102-2.38916 0.54004-1.15186 0.49561-2.05078 1.37744-0.9502 0.92627-1.46973 2.05762-0.10938 0.25293-0.14697 0.37939-0.03418 0.12646-0.03418 0.29395 0 0.16748 0.03418 0.28711 0.0376 0.11963 0.1333 0.35547 0.37939 0.82715 0.97412 1.5415 0.59473 0.71436 1.33643 1.24756 1.45605 1.00488 3.2915 1.20313 0.2085 0.02734 0.64258 0.02734 0.43408 0 0.62891-0.02734 1.20654-0.12647 2.24902-0.60157 1.04248-0.47851 1.8833-1.28857 0.96387-0.93994 1.4834-2.10205 0.0957-0.23584 0.12988-0.35547 0.0376-0.11963 0.0376-0.28711 0-0.16748-0.0376-0.28711-0.03418-0.11963-0.12988-0.35547-0.36572-0.78613-0.93311-1.4834-0.56396-0.70068-1.27832-1.22021-0.84082-0.61523-1.82861-0.9502-0.98779-0.33838-2.02344-0.36572-0.2085 0-0.50244 0.01367z m0.99463 1.18946q1.46973 0.16748 2.67285 1.05273 1.20313 0.88184 1.81836 2.2251l0.08545 0.18115-0.08545 0.19482q-0.62891 1.34668-1.83203 2.22168-1.20313 0.875-2.73096 1.05616-0.18115 0.01367-0.60156 0.01367-0.42041 0-0.60156-0.01367-1.10742-0.12647-2.0542-0.62207-0.94336-0.49902-1.65772-1.32618-0.51611-0.61523-0.85107-1.34326l-0.08545-0.18115 0.08545-0.18115q0.43408-0.95361 1.18945-1.69531 0.75537-0.7417 1.75-1.16211 0.90918-0.39307 1.94483-0.44776 0.14014-0.01367 0.46142 0 0.32129 0.01367 0.49219 0.02735z m-1.03564 1.1621q-0.89893 0.15381-1.44239 0.82715-0.18457 0.22217-0.29052 0.44776-0.10254 0.22559-0.18799 0.51953-0.02734 0.10938-0.03418 0.19482-0.00684 0.08203-0.00684 0.30762 0 0.28027 0.02735 0.43408 0.02734 0.15381 0.11279 0.3794 0.16748 0.4751 0.52978 0.84082 0.36572 0.3623 0.84082 0.52978 0.22559 0.08545 0.3794 0.11279 0.15381 0.02734 0.43408 0.02735 0.28027 0 0.43408-0.02735 0.15381-0.02734 0.3794-0.11279 0.4751-0.16748 0.8374-0.52978 0.36572-0.36572 0.5332-0.84082 0.08545-0.22559 0.1128-0.3794 0.02734-0.15381 0.02734-0.43408 0-0.28027-0.02734-0.43408-0.02734-0.15381-0.1128-0.3794-0.2085-0.55713-0.6665-0.95019-0.45459-0.39307-1.04248-0.51953-0.14014-0.02734-0.42725-0.03418-0.28711-0.00684-0.41015 0.02051z m0.65625 1.16211q0.28027 0.07178 0.52294 0.31788 0.24609 0.24268 0.31788 0.52294 0.02734 0.12646 0.02734 0.29395 0 0.16748-0.02734 0.28027-0.07178 0.29395-0.3042 0.53321-0.229 0.23584-0.53662 0.32129-0.11279 0.02734-0.29395 0.02734-0.18115 0-0.29395-0.02734-0.30762-0.08545-0.54003-0.32129-0.229-0.23926-0.30079-0.53321-0.02734-0.11279-0.02734-0.28027 0-0.16748 0.02734-0.29395 0.05811-0.22559 0.22559-0.43408 0.16748-0.2085 0.40674-0.32129 0.34863-0.18115 0.79639-0.08545z"
                      fill="#94A3B8"
                    ></path>
                  </svg>
                </div>
              </div>
              <div
                data-pencil-name="Remember Row"
                class="box-border w-full h-fit shrink-0 flex flex-row gap-[12px] justify-between items-center"
              >
                <div
                  data-pencil-name="Remember"
                  class="box-border w-fit shrink-0 h-fit flex flex-row gap-[8px] justify-start items-center"
                >
                  <div
                    data-pencil-name="Checkbox"
                    class="box-border w-[18px] shrink-0 h-[18px] flex flex-row gap-0 justify-center items-center bg-[#2563EB] rounded-[5px]"
                  >
                    <svg
                      data-pencil-name="Check"
                      data-icon-name="check"
                      data-icon-set="lucide"
                      viewBox="0 0 13.99993896484375 14"
                      preserveAspectRatio="xMidYMid meet"
                      xmlns="http://www.w3.org/2000/svg"
                      class="box-border w-[12px] shrink-0 h-[12px]"
                    >
                      <path
                        d="M11.48096 2.95313q-0.07178 0.01367-0.12989 0.0581-0.05469 0.04102-3.07617 3.06592l-3.0249 3.00781-1.28857-1.28857q-1.28857-1.28516-1.38086-1.32618-0.08887-0.04443-0.22217-0.04443-0.1333 0-0.23242 0.0376-0.0957 0.03418-0.18799 0.11279-0.08887 0.0752-0.1333 0.1709-0.02734 0.07178-0.03418 0.11279-0.00684 0.04102-0.00684 0.14014l0 0.04102q-0.01367 0.11279 0.04102 0.19824 0.07178 0.10938 0.36572 0.40332 0.19482 0.21191 0.96729 0.98096l1.49707 1.48339q0.28027 0.2666 0.38964 0.33838 0.07178 0.05469 0.18457 0.04102l0.09571 0.01367q0.07178 0 0.14013-0.02734 0.08545-0.07178 0.32129-0.28711 0.23926-0.21875 0.79981-0.76221l2.2832-2.2832q2.08496-2.09863 2.7002-2.71387 0.61524-0.61865 0.64599-0.68701 0.04102-0.08545 0.04102-0.23926 0-0.09912-0.00684-0.14014-0.00684-0.04102-0.03418-0.11279-0.04443-0.08203-0.13672-0.16406-0.08887-0.08545-0.18115-0.11963-0.08887-0.0376-0.20849-0.0376-0.11963 0-0.18799 0.02734z"
                        fill="#FFFFFF"
                      ></path>
                    </svg>
                  </div>
                  <div
                    data-pencil-name="RemLab"
                    class="text-[13px]/[normal] box-border text-[#0F172A] font-[Inter,system-ui,sans-serif] font-normal text-left [white-space:nowrap]"
                  >
                    Ingat saya
                  </div>
                </div>
                <div
                  data-pencil-name="Forgot"
                  class="text-[13px]/[normal] box-border text-[#2563EB] font-[Inter,system-ui,sans-serif] font-semibold text-left [white-space:nowrap]"
                >
                  Lupa kata sandi?
                </div>
              </div>
              <div
                data-pencil-name="Login Button"
                class="box-border w-full h-fit shrink-0 [box-shadow:0px_4px_12px_#2563EB66] flex flex-row gap-[8px] p-[14px] justify-center items-center bg-[#2563EB] rounded-[10px]"
              >
                <div
                  data-pencil-name="Btn Label"
                  class="text-[15px]/[normal] box-border text-[#FFFFFF] font-[Inter,system-ui,sans-serif] font-semibold text-left [white-space:nowrap]"
                >
                  Masuk ke SmartSchool â†’
                </div>
              </div>
              <div
                data-pencil-name="Divider"
                class="box-border w-full h-fit shrink-0 flex flex-row gap-[12px] justify-start items-center"
              >
                <div
                  data-pencil-name="L"
                  class="box-border [flex:1_1_0] h-[1px] flex flex-row gap-0 justify-start items-start bg-[#E2E8F0]"
                ></div>
                <div
                  data-pencil-name="atau"
                  class="text-[12px]/[normal] box-border text-[#64748B] font-[Inter,system-ui,sans-serif] font-normal text-left [white-space:nowrap]"
                >
                  atau
                </div>
                <div
                  data-pencil-name="R"
                  class="box-border [flex:1_1_0] h-[1px] flex flex-row gap-0 justify-start items-start bg-[#E2E8F0]"
                ></div>
              </div>
              <div
                data-pencil-name="SSO"
                class="box-border w-full h-fit shrink-0 flex flex-row gap-[8px] p-[13px] justify-center items-center bg-[#FFFFFF] [outline:1px_solid_#E2E8F0] [outline-offset:-0.5px] rounded-[10px]"
              >
                <svg
                  data-pencil-name="Shield"
                  data-icon-name="shield-check"
                  data-icon-set="lucide"
                  viewBox="0 0 13.99993896484375 14"
                  preserveAspectRatio="xMidYMid meet"
                  xmlns="http://www.w3.org/2000/svg"
                  class="box-border w-[18px] shrink-0 h-[18px]"
                >
                  <path
                    d="M6.84619 0.60156q-0.34863 0.02734-0.68701 0.30762-0.51611 0.42041-1.00147 0.70068-0.48193 0.28027-1.01513 0.4751-0.30762 0.11279-0.58789 0.1709-0.28027 0.05469-0.66992 0.08203-0.23926 0.01367-0.36573 0.07178-0.19482 0.06836-0.37939 0.22217-0.18115 0.15381-0.26319 0.34863l-0.01367 0.03076q-0.07178 0.12646-0.08545 0.22217-0.01367 0.15381-0.02734 0.60156l0 1.78076q0 2.25244 0.01367 2.46094 0.14014 1.6543 1.10742 2.88477 0.14014 0.16748 0.43409 0.46142 0.29395 0.29394 0.48877 0.44775 0.92285 0.7417 2.2832 1.27491 0.46143 0.18115 0.65625 0.23926 0.23926 0.05469 0.43408 0.01367 0.16748-0.02734 0.5332-0.15381 1.35693-0.51953 2.27979-1.20313 0.37939-0.2666 0.71435-0.60498 1.37402-1.3706 1.54151-3.37353 0.01367-0.23584 0.01367-2.46094 0-2.22852-0.02734-2.35498-0.07178-0.32129-0.31787-0.56396-0.24268-0.24609-0.5503-0.31788-0.09912-0.02734-0.37939-0.04101-0.48877-0.02734-0.96387-0.18115-0.81348-0.2666-1.55518-0.77246-0.2666-0.18115-0.64257-0.48877-0.42041-0.33496-0.96729-0.28028z m0.43408 1.34326q0.64258 0.51953 1.32959 0.86817 1.2168 0.61865 2.25244 0.68701l0.21192 0-0.01367 4.31348q0 0.33496-0.02735 0.50244-0.22559 1.34326-1.1416 2.27637-0.91602 0.92969-2.66601 1.57226l-0.21192 0.08545-0.18115-0.05469q-2.32422-0.85449-3.2334-2.22851-0.47852-0.71436-0.63232-1.63721-0.02734-0.18115-0.02735-0.50244l-0.01367-4.32715 0.19483 0q0.86816-0.05469 1.84912-0.48193 0.98096-0.42725 1.83545-1.12793 0.16748-0.14014 0.19482-0.14014 0.02734 0 0.28027 0.19482z m1.32959 3.31885q-0.06836 0.02734-0.12646 0.05127-0.05469 0.02051-1.06299 1.02539l-0.99463 0.99463-0.40674-0.40332q-0.2666-0.2666-0.3623-0.33838-0.12646-0.11279-0.19824-0.14013-0.06836-0.02734-0.19483-0.02735-0.12646 0-0.16748 0.01367-0.04102 0.01367-0.09912 0.04102-0.18115 0.09912-0.28027 0.2666-0.04102 0.08545-0.04102 0.25293l0 0.01367q0 0.14014 0.03418 0.21192 0.0376 0.06836 0.17774 0.22216 0.0957 0.11279 0.48876 0.50586l0.04102 0.04102q0.42041 0.42041 0.55371 0.54687 0.1333 0.12305 0.20166 0.15381 0.14014 0.06836 0.29395 0.05469 0.15381-0.01367 0.28027-0.09912 0.07178-0.05469 1.28857-1.27149 0.86816-0.86816 1.04932-1.06298 0.18457-0.19824 0.21192-0.27002 0.0957-0.27686-0.05811-0.52295-0.15381-0.24609-0.44775-0.25977-0.12646-0.01367-0.18116 0z"
                    fill="#0F172A"
                  ></path>
                </svg>
                <div
                  data-pencil-name="SSO Label"
                  class="text-[14px]/[normal] box-border text-[#0F172A] font-[Inter,system-ui,sans-serif] font-semibold text-left [white-space:nowrap]"
                >
                  Masuk dengan SSO Sekolah
                </div>
              </div>
              <div
                data-pencil-name="Secure"
                class="box-border w-full h-fit shrink-0 flex flex-row gap-[8px] justify-center items-center"
              >
                <svg
                  data-pencil-name="LockS"
                  data-icon-name="lock"
                  data-icon-set="lucide"
                  viewBox="0 0 13.99993896484375 14"
                  preserveAspectRatio="xMidYMid meet"
                  xmlns="http://www.w3.org/2000/svg"
                  class="box-border w-[14px] shrink-0 h-[14px]"
                >
                  <path
                    d="M6.77441 0.60156q-0.72803 0.02734-1.38769 0.3794-0.65625 0.34863-1.12451 0.92285-0.46826 0.57422-0.64942 1.28857l0 0.01367q-0.07178 0.29395-0.08545 0.48536-0.01367 0.18799-0.02734 1.02539l0 1.10742-0.2666 0q-0.40674 0-0.63916 0.04443-0.229 0.04102-0.45117 0.15039-0.32471 0.16748-0.56397 0.44776-0.23584 0.28027-0.34863 0.64599l-0.05469 0.15381 0 4.7168 0.05469 0.15381q0.15723 0.51953 0.56055 0.86132 0.40674 0.3418 0.93994 0.39991 0.14014 0.02734 4.26904 0.02734 4.12891 0 4.26904-0.02734 0.30762-0.02734 0.58789-0.16748 0.32471-0.15381 0.56055-0.44092 0.23926-0.28711 0.35205-0.65283l0.05469-0.15381 0-4.7168-0.05469-0.15381q-0.11279-0.36572-0.35205-0.64599-0.23584-0.28027-0.56055-0.44776-0.22217-0.10938-0.45459-0.15039-0.229-0.04443-0.63574-0.04443l-0.2666 0 0-1.13477q-0.01367-0.81006-0.02734-0.99804-0.01367-0.19141-0.08545-0.47168l0-0.02735q-0.18115-0.71436-0.66651-1.30224-0.48193-0.58789-1.15185-0.93653-0.82715-0.40674-1.79444-0.35205z m0.70069 1.20313q0.75537 0.15381 1.2749 0.7417 0.3623 0.40674 0.50244 0.92627 0.05811 0.19482 0.06494 0.41357 0.00684 0.21533 0.00684 0.9707l0 0.96729-4.64844 0 0-1.06299q0-0.82715 0.01367-1.0083 0.01367-0.18457 0.09912-0.40674 0.22217-0.64258 0.74854-1.06982 0.52637-0.42725 1.19629-0.5127 0.14014-0.01367 0.3623 0 0.22559 0.01367 0.3794 0.04102z m3.85205 5.25q0.08203 0.04443 0.15723 0.12304 0.07861 0.0752 0.11279 0.15381 0.0376 0.0752 0.0581 0.19483 0.02051 0.11621 0.00684 2.19092l0 1.6372q0 0.3623-0.02734 0.48877-0.01367 0.08545-0.07178 0.14014l-0.01367 0.02734q-0.11279 0.12646-0.26661 0.19824l-0.0957 0.04102-8.37402 0-0.0957-0.04102q-0.15381-0.0581-0.26661-0.19824l-0.01367-0.02734q-0.05811-0.06836-0.07178-0.14014-0.02734-0.12646-0.02734-0.48877l0-1.65088q-0.01367-2.08838 0.01367-2.20117 0.01367-0.18115 0.1333-0.31445 0.11963-0.1333 0.28711-0.17432 0.06836-0.01367 4.25537-0.01367l4.20069 0.01367 0.09912 0.04102z"
                    fill="#94A3B8"
                  ></path>
                </svg>
                <div
                  data-pencil-name="SecT"
                  class="text-[12px]/[normal] box-border [flex:1_1_0] text-[#64748B] font-[Inter,system-ui,sans-serif] font-normal text-center"
                >
                  Dilindungi enkripsi SSL 256-bit â€¢ Belum punya akses? Hubungi Operator
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>

