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

## 실제 동작 검증
`api.php`의 4개 액션(store/retrieve/wet/theft)과 `slotTable.php`/`logTable.php`/`userTable.php`를 실제 PHP 내장 서버 + MariaDB로 기동해 전부 실행 확인했다. 회수 시 UID 불일치(FAIL: UID_MISMATCH), 존재하지 않는 action(ERROR: unknown action), 슬롯 파라미터 누락(ERROR: invalid slot) 등 예외 케이스도 검증됨.

**수정 사항**: `userTable.php`가 원래 `$row['uid']`를 읽고 있었는데, `user` 테이블의 실제 컬럼명은 `card_uid`라서(스키마 참고) 등록 사용자 UID 칸이 항상 빈 값으로 표시되는 문제가 있었다. `$row['card_uid']`로 수정해 반영했다.
