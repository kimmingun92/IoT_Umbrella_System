# raspberrypi/

관리실에서 동작하는 라즈베리파이 측 코드 3종.

| 파일 | 역할 |
|---|---|
| `iot_server.c` | `iot_server` — pthread 기반 멀티스레드 TCP 서버. 클라이언트마다 스레드를 붙여 아두이노·DB클라이언트·블루투스클라이언트를 동시 수용하고, ID 태그로 메시지를 라우팅한다. |
| `iot_client_sensor_device.c` | `iot_client_sensor_device` (ID: `UMB_DB`) — 서버로부터 받은 아두이노 메시지를 MySQL에 INSERT/UPDATE한다. STORE/RETRIEVE/WET/THEFT 4개 액션 처리. |
| `iot_client_bluetooth.c` | `iot_client_bluetooth` (ID: `YGY_BLT`) — 서버와 STM32 사이를 중계. STM32 요청 시 자체 MySQL 커넥션으로 DB를 조회하고 결과를 Bluetooth로 응답. |
| `iot_client.c` | 범용 콘솔 클라이언트 — 관리자 명령(`SLOT@1@OPEN` 등) 전송이나 디버깅용. |

## 빌드
```bash
gcc -o iot_server iot_server.c -lpthread
gcc -o iot_client_sensor_device iot_client_sensor_device.c -lmysqlclient -lpthread
gcc -o iot_client_bluetooth iot_client_bluetooth.c -lmysqlclient -lbluetooth -lpthread
gcc -o iot_client iot_client.c -lpthread
```

## 실행 순서
```bash
# 1) idpasswd.txt.example 을 idpasswd.txt 로 복사 후 실제 계정으로 수정
cp idpasswd.txt.example idpasswd.txt

# 2) 서버 먼저 실행
./iot_server 5000

# 3) DB 클라이언트, 블루투스 클라이언트 순서 상관없이 실행
./iot_client_sensor_device 127.0.0.1 5000 UMB_DB
sudo ./iot_client_bluetooth 127.0.0.1 5000 YGY_BLT
```

## 참고
- `iot_client_bluetooth.c` 상단의 `BT_ADDR`는 실제 STM32측 블루투스 모듈 MAC 주소로 교체해야 한다.
- DB 접속 정보(`localhost`/`iot`/`pwiot`/`iotdb`)는 각 파일 상단 매크로/변수에서 환경에 맞게 수정한다.
