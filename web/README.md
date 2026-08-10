# web/

DB 조회·연동용 웹 페이지. Apache + PHP(mysqli)로 동작하며, MariaDB `iotdb`에 직접 연결한다.

| 파일 | 역할 |
|---|---|
| `api.php` | HTTP GET 기반 DB 연동 API. `?action=store\|retrieve\|wet\|theft&slot=N&uid=...&dry=N` 형식으로 호출하면 `slot`/`user`/`log` 테이블을 갱신한다. TCP 프로토콜(`iot_client_sensor_device.c`)과는 별개의 경로로, 관리자 페이지나 테스트 스크립트에서 직접 호출하는 용도로 쓰인다. |
| `slotTable.php` | 전체 슬롯 상태 조회 (5초 자동 새로고침) |
| `logTable.php` | 최근 이벤트 로그 20건 조회 (5초 자동 새로고침) |
| `userTable.php` | 등록된 RFID 사용자 목록 조회 (5초 자동 새로고침) |
| `index.html` | 위 3개 페이지를 3분할(frameset)로 보여주는 관제 대시보드 |

## 배포
```bash
sudo cp *.php index.html /var/www/html/
```

DB 접속 정보(`localhost`/`iot`/`pwiot`/`iotdb`)는 각 PHP 파일 상단에서 환경에 맞게 수정한다.

## api.php 액션
| action | 파라미터 | 동작 |
|---|---|---|
| `store` | `slot`, `uid`, `dry` | `user` 테이블에 UID 등록, `slot`을 USING으로 갱신, `log`에 STORE 기록 |
| `retrieve` | `slot`, `uid` | `slot.assigned_uid`와 비교해 일치 시 EMPTY로 갱신 + PICKUP 로그, 불일치 시 AUTH_FAIL 로그 |
| `wet` | `slot`, `dry` | `dry` 값에 따라 slot을 DRYING/DRY_DONE으로 갱신 |
| `theft` | `slot` | `slot`을 THEFT로 갱신, THEFT 로그 기록 |

예: `GET /api.php?action=store&slot=1&uid=A3F20C11&dry=0`
