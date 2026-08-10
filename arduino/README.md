# arduino/

우산꽂이 외부에 부착되는 현장 제어부. `umbrella_system.ino` 하나로 3슬롯 전체(RFID, 서보모터, IR센서, 수위센서, DC모터, 부저·LED)를 통합 제어한다.

## 핀 배치
| 핀 | 용도 | 비고 |
|---|---|---|
| D2–D4 | IR 센서 1~3 | 슬롯 1~3 |
| D5–D7 | 서보모터 1~3 | 슬롯 1~3 |
| D8 | 부저 | |
| D9–D13 | RFID (RST/SS/MOSI/MISO/SCK) | SPI |
| A0 | 수위센서 | analogRead |
| A1 / A2 | WiFi 모듈 RX / TX | SoftwareSerial |
| A3 | DC모터 팬 | ON/OFF |
| A4 / A5 | LCD SDA / SCL | I2C |

## 필요 라이브러리
- SPI, MFRC522, Wire, LiquidCrystal_I2C, Servo, MsTimer2, WiFiEsp, SoftwareSerial

## 설정
스케치 상단에서 WiFi/서버 정보를 실제 환경에 맞게 수정한다.
```cpp
#define AP_SSID       "KCCI601"
#define AP_PASS       "@kcci601@"
#define SERVER_NAME   "10.10.16.63"
#define SERVER_PORT   5000
```

## 동작 요약
1. 빈 슬롯에 RFID 태그 → 서보 열림 → 우산 삽입(IR 감지) → 10초 유지 확인 후 서보 잠금 → DB에 보관 기록 전송
2. 본인 카드 재태그 → 저장된 UID와 비교해 일치 시에만 서보 열림 → IR로 제거 확인 → DB 회수 기록 전송
3. 타인 카드 태그 시 UID 불일치로 서보 유지, 강제 인출 시 도난 판정 → 부저 경고 + DB 도난 로그
4. 수위센서 값을 히스테리시스(HIGH/MID/LOW 3단계)로 판단해 DC모터 속도 제어, 10초마다 건조% DB 전송
